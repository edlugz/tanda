<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\Models\TandaTransaction;
use EdLugz\Tanda\TandaClient;
use Illuminate\Support\Str;

class Airtime extends TandaClient
{
    /**
     * send airtime request end point on Tanda API.
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
     * The result URL assigned for airtime transactions on Tanda API.
     *
     * @var string
     */
    protected string $resultUrl;

    /**
     * Airtime constructor.
     *
     * @throws \EdLugz\Tanda\Exceptions\TandaRequestException
     */
    public function __construct()
    {
        parent::__construct();

        $this->orgId = config('tanda.organisation_id');

        $this->endPoint = 'io/v2/organizations/'.$this->orgId.'/requests';

        $this->resultUrl = config('tanda.result_url');
    }

    /**
     * Purchase pinless prepaid airtime.
     *
     * @param       $merchantWallet
     * @param       $serviceProviderId
     * @param       $amount
     * @param       $mobileNumber
     * @param array $customFieldsKeyValue
     *
     * @return \EdLugz\Tanda\Models\TandaTransaction
     */
    public function prepaid(
        $merchantWallet,
        $serviceProviderId,
        $amount,
        $mobileNumber,
        array $customFieldsKeyValue = []
    ): TandaTransaction {
        $reference = (string) Str::ulid();

        $parameters = [
            'commandId'         => 'MerchantTopupFlexi',
            'serviceProviderId' => $serviceProviderId,
            'requestParameters' => [
                [
                    'id'    => 'merchantWallet',
                    'label' => 'wallet',
                    'value' => $merchantWallet,
                ],
                [
                    'id'    => 'amount',
                    'value' => $amount,
                    'label' => 'Amount',
                ],
                [
                    'id'    => 'accountNumber',
                    'value' => $mobileNumber,
                    'label' => 'Phone No.',
                ],
            ],
            'referenceParameters' => [
                [
                    'id'    => 'resultUrl',
                    'value' => $this->resultUrl,
                    'label' => 'Hook',
                ],
            ],
            'reference' => $reference,
        ];

        /** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference'   => $reference,
            'service_provider'    => 'AIRTIME',
            'merchant_wallet'     => $merchantWallet,
            'amount'              => $amount,
            'account_number'      => $mobileNumber,
            'service_provider_id' => $serviceProviderId,
            'json_request'        => json_encode($parameters),
        ], $customFieldsKeyValue));

        try {
            $response = $this->call($this->endPoint, ['json' => $parameters], 'POST');

            $payment->update(
                [
                    'json_response' => json_encode($response),
                ]
            );
        } catch (TandaRequestException $e) {
            $response = [
                'status'       => $e->getCode(),
                'responseCode' => $e->getCode(),
                'message'      => $e->getMessage(),
            ];

            $response = (object) $response;
        }

        $data = [
            'response_status'  => $response->status,
            'response_message' => $response->message,
        ];

        if ($response->status == '000001') {
            $data = array_merge($data, [
                'transaction_id' => $response->id,
            ]);
        }

        $payment->update($data);

        return $payment;
    }
}
