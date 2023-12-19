<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Models\TandaTransaction;
use EdLugz\Tanda\TandaClient;
use Illuminate\Support\Str;

class B2B extends TandaClient
{
    /**
     * send b2b request end point on Tanda API.
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
     * The result URL assigned for b2b transactions on Tanda API.
     *
     * @var string
     */
    protected string $resultUrl;

    /**
     * B2B constructor.
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->orgId = config('tanda.organisation_id'); 
		
		$this->endPoint = 'io/v2/organizations/'.$this->orgId.'/requests';
		
		$this->resultUrl = config('tanda.result_url');;

		
    }

    /**
     * Send money from merchant wallet to till
     
      	@param string merchantWallet
      	@param string amount
      	@param string till
      	@param string contact
      	@param array $customFieldsKeyValue
		
		@return TandaTransaction
     */
    public function buygoods(
		string $merchantWallet, 
		string $amount, 
		string $till, 
		string $contact,
		array $customFieldsKeyValue = []
	): TandaTransaction {
		
		$reference = (string) Str::ulid();
		
		/** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference' => $reference,
            'service_provider' => 'MPESA',
            'merchant_wallet' => $merchantWallet,
            'amount' => $amount,
            'contact' => $contact,
            'service_provider_id' => $till
        ], $customFieldsKeyValue));
		
        $parameters = [
			"commandId" => "MerchantBuyGoods",
			"serviceProviderId" => "MPESA",
			"requestParameters" =>  [
				[
					"id" => "merchantWallet",
					"label" => "merchantWallet",
					"value" => $merchantWallet
				],
				[				
					"id" => "merchantNumber",
					"label" => "merchantNumber",
					"value" => $till
				],
				[
					"id" => "customerContact",
					"label" => "customerContact",
					"value" => $contact
				],
				[
					"id" => "amount",
					"label" => "amount",
					"value" => $amount
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

        
		try {
			$response = $this->call($this->endPoint, ['json' => $parameters], 'POST');
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
     * Send money from merchant wallet to paybill business numbers
     
      	@param string merchantWallet
      	@param string amount
      	@param string paybill
      	@param string accountNumber
      	@param string contact
      	@param array customFieldsKeyValue
		
		@return TandaTransaction
     */
    public function paybill(
		string $merchantWallet, 
		string $amount, 
		string $paybill, 
		string $accountNumber, 
		string $contact,
		array $customFieldsKeyValue = []
	): TandaTransaction {
		
		$reference = (string) Str::ulid();
		
		/** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference' => $reference,
            'service_provider' => 'MPESA',
            'merchant_wallet' => $merchantWallet,
            'amount' => $amount,
            'service_provider_id' => $paybill,
            'account_number' => $accountNumber,
            'contact' => $contact,
        ], $customFieldsKeyValue));
		
        $parameters = [
			"commandId" => "MerchantBillPay",
			"serviceProviderId" => "MPESA",
			"requestParameters" =>  [
				[
					"id" => "merchantWallet",
					"label" => "merchantWallet",
					"value" => $merchantWallet,
				],
				[
					"id" => "businessNumber",
					"label" => "businessNumber",
					"value" => $paybill,
				],
				[
					"id" => "accountNumber",
					"label" => "accountNumber",
					"value" => $accountNumber,
				],
				[
					"id" => "customerContact",
					"label" => "customerContact",
					"value" => $contact,
				],
				[
					"id" => "amount",
					"label" => "amount",
					"value" => $amount,
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
       
		try {
			$response = $this->call($this->endPoint, ['json' => $parameters], 'POST');
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