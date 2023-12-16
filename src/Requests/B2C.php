<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Models\TandaTransaction;
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
		
        $this->orgId = config('tanda.org_id'); 
		
		$this->endPoint = 'io/v2/organizations/'.$this->orgId.'/requests';
		
		$this->resultUrl = config('tanda.result_url');
		
    }

    /**
     * Send money from merchant wallet to bank
     
      	@param string merchantWallet
      	@param string bankCode - as provided
      	@param string amount
      	@param string accountNumber
      	@param string accountName
      	@param string narration
		
		@return TandaTransaction
     */
    public function bank(
		string $merchantWallet, 
		string $bankCode, 
		string $amount, 
		string $accountNumber, 
		string $accountName, 
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
					"label" => "serviceProviderId",
					"value" => $bankCode
				],
				[
					"id" => "accountNumber",
					"label" => "Bank Ac Number",
					"value" => $accountNumber
				],
				[
					"id" => "accountName",
					"label" => "accountName",
					"value" => $accountName
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
     * Send money from merchant wallet to mobile wallet(s)
     
      	@param string merchantWallet
      	@param string serviceProviderId - (MPESA / AIRTELMONEY / TKASH / EQUITEL)
      	@param string amount
      	@param string mobileNumber
      	@param string narration
		
		@return string
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
            'account_number' => $mobileNumber
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
			"referenceParameters" =>  [
				"resultUrl" => $this->resultUrl
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
     * Process results for b2c function.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \EdLugz\Tanda\Models\TandaTransaction
     */
    public function result(Request $request): TandaTransaction
    {
        $transaction = TandaTransaction::where('request_id', $request->input('transactionId'))->first();
		
		if($request->input('status') == '000000'){
			$data = [
				'request_status' => $request->input('status'),
				'request_message' => $request->input('message'),
				'receipt_number' => $request->input('receiptNumber'),
				'transaction_receipt' => $request->input('value'),
				'timestamp' => $request->input('timestamp'),
			];
		} else {
			$data = [
				'request_status' => $request->input('status'),
				'request_message' => $request->input('message'),
				'timestamp' => $request->input('timestamp'),
			];
		}
		
        $transaction->update($data);

        return $transaction;
    }
}