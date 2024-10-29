<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\Helpers\TandaHelper;
use EdLugz\Tanda\Models\TandaTransaction;
use EdLugz\Tanda\TandaClient;
use Illuminate\Support\Str;

class P2P extends TandaClient
{
    /**
     * send p2p request end point on Tanda API.
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
     * The result URL assigned for p2p transactions on Tanda API.
     *
     * @var string
     */
    protected string $resultUrl;

    /**
     * P2P constructor.
     *
     * @throws TandaRequestException
     */
    public function __construct(string $resultUrl = null)
    {
        parent::__construct();

        $this->orgId = config('tanda.organisation_id');

        $this->endPoint = 'io/v2/organizations/'.$this->orgId.'/requests';

        $this->resultUrl = $resultUrl ?? TandaHelper::getPaymentResultUrl();
    }

    /**
     * Send money from one Sub wallet to another Sub wallet instantly.
     *
     * @param string $senderWallet
     * @param string $receiverWallet
     * @param string $amount
     * @param array $customFieldsKeyValue
     *
     * @return TandaTransaction
     */
    public function send(
        string $senderWallet,
        string $receiverWallet,
        string $amount,
        array $customFieldsKeyValue = [],
    ): TandaTransaction {
        $reference = (string) Str::ulid();

        $parameters = [
            'commandId'         => 'MerchantToMerchantPayment',
            'serviceProviderId' => 'TANDA',
            'requestParameters' => [
                [
                    'id'    => 'merchantWallet',
                    'label' => 'merchantWallet',
                    'value' => $senderWallet,
                ],
                [
                    'id'    => 'amount',
                    'label' => 'amount',
                    'value' => $amount,
                ],
                [
                    'id'    => 'destMerchantWallet',
                    'label' => 'destMerchantWallet',
                    'value' => $receiverWallet,
                ],
            ],
            'referenceParameters' => [
                [
                    'id'    => 'resultUrl',
                    'lable' => 'resultUrl',
                    'value' => $this->resultUrl,
                ],
            ],
            'reference' => $reference,
        ];

        /** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference' => $reference,
            'service_provider'  => 'TANDA',
            'merchant_wallet'   => $senderWallet,
            'amount'            => $amount,
            'account_number'    => $receiverWallet,
            'json_request'      => json_encode($parameters),
        ], $customFieldsKeyValue));

        try {
            $response = $this->call($this->endPoint, ['json' => $parameters], 'POST');

            $payment->update(
                [
                    'json_response' => json_encode($response),
                ]
            );
        } catch(TandaRequestException $e) {
            $response = [
                'status'         => $e->getCode(),
                'responseCode'   => $e->getCode(),
                'message'        => $e->getMessage(),
            ];

            $response = (object) $response;
        }

        $data = [
            'response_status'        => $response->status,
            'response_message'       => $response->message,
        ];

        if ($response->status == '000001') {
            $data = array_merge($data, [
                'transaction_id'  => $response->id,
            ]);
        }

        $payment->update($data);

        return $payment;
    }
}
