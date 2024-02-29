<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Models\TandaTransaction;
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
     * @throws \EdLugz\Tanda\Exceptions\TandaRequestException
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
	 * @param string $reference
	 *
	 * @return TandaTransaction
     *@throws  TandaRequestException
	 *
	 */
    public function status(string $reference) : TandaTransaction
    {
		$transaction = TandaTransaction::where('payment_reference', $reference)->first();
		
		try {
			
			$response = $this->call($this->endPoint.''.$reference.'', [], 'GET');
			
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

        if ($response->status == '000001') {
            $data = array_merge($data, [
                'receipt_number' => $response->receiptNumber,
				'transaction_receipt' => $response->resultParameters[0]['value'],
				'timestamp' => $response->datetimeCompleted,
            ]);
        }

        $transaction->update($data);

        return $transaction;
    }
	
}