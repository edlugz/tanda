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
	 * @param string $reference
	 *
	 * @return array
     *
	 */
    private function status(string $reference) : array
    {
        try {

            $response = $this->call($this->endPoint . $reference, [], 'GET');

        } catch (TandaRequestException $e) {
            $response = [
                'status' => $e->getCode(),
                'responseCode' => $e->getCode(),
                'message' => $e->getMessage(),
            ];

            $response = (object)$response;
        }

        $data = [
            'request_status' => $response->status,
            'request_message' => $response->message,
        ];

        if ($response->status == '000000') {

            $transactionReceipt = $response->receiptNumber;

            if ($response->resultParameters) {
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
                'timestamp' => date('Y-m-d H:i:s', strtotime($response->datetimeCompleted)),
            ]);

        } else {

            $data = array_merge($data, [
                'request_status' => $response->status,
                'request_message' => $response->message
            ]);

        }

        return $data;
    }

    /**
     * @throws TandaRequestException
     */
    public function fundingCheck(string $reference) : TandaFunding
    {
        $funding = TandaFunding::where('funding_reference', $reference)->first();

        $data = $this->status($reference);

        $funding->update($data);

        return $funding;

    }


    /**
     * @throws TandaRequestException
     */
    public function transactionCheck(string $reference) : TandaTransaction
    {
        $transaction = TandaTransaction::where('payment_reference', $reference)->first();

        $data = $this->status($reference);

        $transaction->update($data);

        return $transaction;

    }


}