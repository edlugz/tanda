<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Models\TandaTransaction;
use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\TandaClient;
use Illuminate\Support\Str;

class B2C extends TandaClient
{
    /**
     * send b2c request end point on Tanda API.
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
     * The result URL assigned for b2c transactions on Tanda API.
     *
     * @var string
     */
    protected string $resultUrl;

    /**
     * B2C constructor.
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->orgId = config('tanda.organisation_id'); 
		
		$this->endPoint = 'io/v2/organizations/'.$this->orgId.'/requests';
		
		$this->resultUrl = config('tanda.result_url');
		
    }

    /**
     * Send money from merchant wallet to bank
     * @param string $merchantWallet
     * @param string $bankCode
     * @param string $amount
     * @param string $accountNumber
     * @param string $narration
     * @param array $customFieldsKeyValue
     * @return \EdLugz\Tanda\Models\TandaTransaction
     */
    public function bank(
		string $merchantWallet, 
		string $bankCode, 
		string $amount, 
		string $accountNumber,
		string $narration,
		array $customFieldsKeyValue = []
	): TandaTransaction {
		$reference = (string) Str::ulid();
		
		/** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference' => $reference,
            'service_provider' => 'PESALINK',
            'merchant_wallet' => $merchantWallet,
            'amount' => $amount,
            'account_number' => $accountNumber,
            'service_provider_id' => $bankCode
        ], $customFieldsKeyValue));
		
        $parameters = [
			"commandId" => "MerchantToBankPayment",
			"serviceProviderId" => "PESALINK",
			"requestParameters" =>  [
				[
					"id" => "merchantWallet",
					"label" => "merchantWallet",
					"value" => $merchantWallet
				],
				[
					"id" => "serviceProviderId",
					"label" => "Bank Code",
					"value" => $bankCode
				],
				[
					"id" => "accountNumber",
					"label" => "Bank Ac Number",
					"value" => $accountNumber
				],
				[
					"id" => "amount",
					"label" => "amount",
					"value" => $amount
				],
				[
					"id" => "narration",
					"label" => "narration",
					"value" => $narration
				]
			],
			"referenceParameters" =>  [
				[
					"id" => "resultUrl",
					"label" => "resultUrl",
					"value" => $this->resultUrl,
				]
			],
			"reference" => $reference
        ];
		
		$payment->update(['json_request' => json_encode($parameters)]);
		
		try {
			$response = $this->call($this->endPoint, ['json' => $parameters], 'POST');
			
			$payment->update(
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

        $payment->update($data);

        return $payment;
    }

    /**
     * Send money from merchant wallet to mobile wallet(s)
     * @param string $merchantWallet
     * @param string $serviceProviderId - (MPESA / AIRTELMONEY / TKASH / EQUITEL)
     * @param string $amount
     * @param string $mobileNumber
     * @param array $customFieldsKeyValue
     * @return \EdLugz\Tanda\Models\TandaTransaction
     */
    public function mobile(
		string $merchantWallet, 
		string $serviceProviderId, 
		string $amount, 
		string $mobileNumber,
		array $customFieldsKeyValue = []
	): TandaTransaction {
		$reference = (string) Str::ulid();
		
		/** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference' => $reference,
            'service_provider' => 'MOBILE',
            'merchant_wallet' => $merchantWallet,
            'amount' => $amount,
            'account_number' => $mobileNumber,
            'service_provider_id' => $serviceProviderId
        ], $customFieldsKeyValue));
		
        $parameters = [
			"commandId" => "MerchantToMobilePayment",
			"serviceProviderId" => $serviceProviderId,
			"requestParameters" =>  [
				[
					"id" => "merchantWallet",
					"label" => "merchantWallet",
					"value" => $merchantWallet
				],
				[
					"id" => "accountNumber",
					"label" => "Mobile Number",
					"value" => $mobileNumber
				],
				[
					"id" => "amount",
					"label" => "amount",
					"value" => $amount
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
		
		$payment->update(['json_request' => json_encode($parameters)]);
        
		try {
			$response = $this->call($this->endPoint, ['json' => $parameters], 'POST');
			
			$payment->update(
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

        $payment->update($data);

        return $payment;
    }
	
}