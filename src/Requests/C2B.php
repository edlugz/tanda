<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Models\TandaFunding;
use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\TandaClient;
use Illuminate\Support\Str;

class C2B extends TandaClient
{
    /**
     * send c2b request end point on Tanda API.
     *
     * @var string
     */
    protected string $endPoint;
	
	/**
     * The organisation ID assigned for the application on Tanda API.
     *
     * @var string
     */
    protected string $orgId;

	/**
     * The result URL assigned for c2b transactions on Tanda API.
     *
     * @var string
     */
    protected string $resultUrl;

    /**
     * C2B constructor.
     * @throws \EdLugz\Tanda\Exceptions\TandaRequestException
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->orgId = config('tanda.organisation_id'); 
		
		$this->endPoint = 'io/v2/organizations/'.$this->orgId.'/requests';
		
		$this->resultUrl = config('tanda.c2b_result_url');		
    }

    /**
     * Receive payments
     * @param string $serviceProviderId - MPESA/AIRTELMONEY
     * @param string $merchantWallet
     * @param string $mobileNumber
     * @param string $amount
     * @param array $customFieldsKeyValue
     * @return \EdLugz\Tanda\Models\TandaFunding
     */
    public function request(
		string $serviceProviderId, 
		string $merchantWallet, 
		string $mobileNumber, 
		string $amount,
		array $customFieldsKeyValue = []
	) : TandaFunding {
		
		$reference = (string) Str::ulid();
		
		/** @var TandaFunding $funding */
        $funding = TandaFunding::create(array_merge([
			'fund_reference' => $reference,
            'service_provider' => $serviceProviderId,
            'account_number' => $mobileNumber,
            'amount' => $amount
        ], $customFieldsKeyValue));
		
        $parameters = [
			"commandId" => "CustomerPayment",
			"serviceProviderId" => $serviceProviderId,
			"requestParameters" =>  [
				[
					"id" => "merchantWallet",
					"label" => "merchantWallet",
					"value" => $merchantWallet
				],				
				[
					"id" => "accountNumber",
					"label" => "accountNumber",
					"value" => $mobileNumber,
				],
				[
					"id" => "amount",
					"label" => "amount",
					"value" => $amount,
				]
			],
			"referenceParameters" => [
				[
					"id" => "resultUrl",
					"lable" => "resultUrl",
					"value" =>  $this->resultUrl
				]
			],
			"reference" => $reference
        ];

        
		try {
			$response = $this->call($this->endPoint, ['json' => $parameters], 'POST');
			
			$funding->update(
				[
					'json_response' => json_encode($response)
				]
			);
			
		} catch(TandaRequestException $e){
			$response = [
                'status'         => $e->getCode(),
                'responseCode'   => $e->getCode(),
                'message'        => $e->getMessage(),
            ];

            $response = (object) $response;
		}
		
		$data = [
            'response_status'      => $response->status,
            'response_message'       => $response->message,
        ];

        if ($response->status == '000001') {
            $data = array_merge($data, [
                'transaction_id'  => $response->id
            ]);
        }

        $funding->update($data);

        return $funding;
    }
	
}