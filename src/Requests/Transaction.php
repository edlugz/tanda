<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Models\TandaTransaction;
use EdLugz\Tanda\Models\TandaFunding;
use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\TandaClient;

class Transaction extends TandaClient
{
	/**
     * Check transaction status end point on Tanda API.
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
     * Transaction constructor.
     * @throws TandaRequestException
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->orgId = config('tanda.organisation_id'); 
		
		$this->endPoint = 'io/v2/organizations/'.$this->orgId.'/requests/';
		
    }
	
    /**
     * Transaction status query
	 *
	 * @param string $transactionType
	 * @param string $reference
	 *
	 * @return TandaTransaction | TandaFunding
     *@throws  TandaRequestException
	 *
	 */
    public function status(string $transactionType, string $reference) : TandaTransaction|TandaFunding  
    {
        if($transactionType === 'funding'){
            $transaction = TandaFunding::where('transaction_id', $reference)->first();
        } else {
		    $transaction = TandaTransaction::where('payment_reference', $reference)->first();
        }
        if($transaction)
            try {
                
                $response = $this->call($this->endPoint .$reference, [], 'GET');
                
            } catch(TandaRequestException $e){
                $response = [
                    'status'         => $e->getCode(),
                    'responseCode'   => $e->getCode(),
                    'message'        => $e->getMessage(),
                ];

                $response = (object) $response;
            }
            
            $data = [
                'request_status'      => $response->status,
                'request_message'       => $response->message,
            ];
            
            if($response->status == '000001'){
            
                $transactionReceipt = $response->receiptNumber;
                
                if($response->resultParameters){
                    $params = $response->resultParameters;
                    $keyValueParams = [];
                    foreach ($params as $param) {
                        $keyValueParams[$param['id']] = $param['value'];
                    }

                    $transactionReceipt = $keyValueParams['transactionRef'];
                }
                
                $data = array_merge($data, [
                    'request_status' => $response->status,
                    'request_message' => $response->message,
                    'receipt_number' => $response->receiptNumber,
                    'transaction_reference' => $transactionReceipt,
                    'timestamp' => $response->timestamp,
                ]);

            } else {

                $data = array_merge($data, [
                    'request_status' => $response->status,
                    'request_message' => $response->message,
                    'timestamp' => $response->timestamp,
                ]);

            }
            

            $transaction->update($data);

        return $transaction;
    }
	
}