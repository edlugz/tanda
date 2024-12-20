<?php

namespace EdLugz\Tanda\Requests;

use EdLugz\Tanda\Exceptions\TandaRequestException;
use EdLugz\Tanda\Helpers\TandaHelper;
use EdLugz\Tanda\Models\TandaTransaction;
use EdLugz\Tanda\TandaClient;
use Illuminate\Support\Str;

class Utility extends TandaClient
{
    /**
     * utility request end point on Tanda API.
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
     * The result URL assigned for utility transactions on Tanda API.
     *
     * @var string
     */
    protected string $resultUrl;

    /**
     * Utility constructor.
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
     * Pay for Electricity or Water.
     *
     * @param string $serviceProviderId    serviceProviderId - KPLC POSTPAID / NAIROBI_WTR
     * @param string $amount               amount
     * @param string $accountNumber        accountNumber
     * @param string $merchantWallet        merchantWallet
     * @param array  $customFieldsKeyValue customFieldsKeyValue
     *
     * @return TandaTransaction
     */
    public function postpaid(
        string $merchantWallet,
        string $serviceProviderId,
        string $amount,
        string $accountNumber,
        array $customFieldsKeyValue = []
    ): TandaTransaction {
        $reference = (string) Str::ulid();

        $parameters = [
            'commandId'         => 'MerchantBillPay',
            'serviceProviderId' => $serviceProviderId,
            'requestParameters' => [
                [
                    'id'    => 'accountNumber',
                    'label' => 'Account',
                    'value' => $accountNumber,
                ],
                [
                    'id'    => 'merchantWallet',
                    'label' => 'merchant',
                    'value' => $merchantWallet,
                ],
                [
                    'id'    => 'amount',
                    'label' => 'Amount',
                    'value' => $amount,
                ],
            ],
            'referenceParameters' => [
                [
                    'id'    => 'resultUrl',
                    'label' => 'Callback',
                    'value' => $this->resultUrl,
                ],

            ],
            'reference' => $reference,
        ];

        /** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference' => $reference,
            'service_provider'  => $serviceProviderId,
            'merchant_wallet' => $merchantWallet,
            'amount'              => $amount,
            'account_number'      => $accountNumber,
            'service_provider_id' => $serviceProviderId,
            'json_request'        => json_encode($parameters),
        ], $customFieldsKeyValue));

        $payment->update([]);

        try {
            $response = $this->call($this->endPoint, ['json' => $parameters]);

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

    /**
     * Purchase KPLC Prepaid Tokens
     * serviceProviderId - KPLC.
     *
     * @param string $merchantWallet
     * @param string $amount
     * @param string $accountNumber
     * @param string $contact
     * @param array  $customFieldsKeyValue
     *
     * @return TandaTransaction
     */
    public function prepaid(
        string $merchantWallet,
        string $amount,
        string $accountNumber,
        string $contact,
        array $customFieldsKeyValue = []
    ): TandaTransaction {
        $reference = (string) Str::ulid();

        $parameters = [
            'commandId'         => 'MerchantVoucherFlexi',
            'serviceProviderId' => 'KPLC',
            'requestParameters' => [
                [
                    'id'    => 'merchantWallet',
                    'label' => 'merchant wallet',
                    'value' => $merchantWallet,
                ],
                [
                    'id'    => 'accountNumber',
                    'label' => 'Account',
                    'value' => $accountNumber,
                ],
                [
                    'id'    => 'customerContact',
                    'label' => 'Contact',
                    'value' => $contact,
                ],
                [
                    'id'    => 'amount',
                    'label' => 'Amount',
                    'value' => $amount,
                ],
            ],
            'referenceParameters' => [
                [
                    'id'    => 'resultUrl',
                    'label' => 'Callback',
                    'value' => $this->resultUrl,
                ],

            ],
            'reference' => $reference,
        ];

        /** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference'   => $reference,
            'service_provider'    => 'KPLC-PREPAID',
            'merchant_wallet'     => $merchantWallet,
            'amount'              => $amount,
            'account_number'      => $accountNumber,
            'service_provider_id' => 'KPLC',
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

    /**
     * Pay for subscription TV.
     *
     * @param       $merchantWallet
     * @param       $serviceProviderId    - GOTV / ZUKU / STARTIMES / DSTV
     * @param       $amount
     * @param       $accountNumber
     * @param array $customFieldsKeyValue
     *
     * @return \EdLugz\Tanda\Models\TandaTransaction
     */
    public function tv(
        $merchantWallet,
        $serviceProviderId,
        $amount,
        $accountNumber,
        array $customFieldsKeyValue = []
    ): TandaTransaction {
        $reference = (string) Str::ulid();

        $parameters = [
            'commandId'         => 'MerchantTopupFix',
            'serviceProviderId' => $serviceProviderId,
            'requestParameters' => [
                [
                    'id'    => 'merchantWallet',
                    'label' => 'wallet',
                    'value' => $merchantWallet,
                ],
                [
                    'id'    => 'accountNumber',
                    'label' => 'Account',
                    'value' => $accountNumber,
                ],
                [
                    'id'    => 'amount',
                    'label' => 'Amount',
                    'value' => $amount,
                ],
            ],
            'referenceParameters' => [
                [
                    'id'    => 'resultUrl',
                    'label' => 'Callback',
                    'value' => $this->resultUrl,
                ],

            ],
            'reference' => $reference,
        ];

        /** @var TandaTransaction $payment */
        $payment = TandaTransaction::create(array_merge([
            'payment_reference'   => $reference,
            'service_provider'    => $serviceProviderId,
            'merchant_wallet'     => $merchantWallet,
            'amount'              => $amount,
            'account_number'      => $accountNumber,
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
