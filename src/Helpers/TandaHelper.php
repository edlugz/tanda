<?php

namespace EdLugz\Tanda\Helpers;

use EdLugz\Tanda\Models\TandaFunding;
use EdLugz\Tanda\Models\TandaTransaction;
use Illuminate\Http\Request;

class TandaHelper
{
    /**
     * get mobile money / airtime provider from mobile number.
     *
     * @param string $mobileNumber - 0XXXXXXXXX
     * @param bool   $airtime
     *
     * @return string
     */
    public static function serviceProvider(string $mobileNumber, bool $airtime = false): string
    {
        $safaricom = '/(?:0)?((?:(?:7(?:(?:[01249][0-9])|(?:5[789])|(?:6[89])))|(?:1(?:[1][0-5])))[0-9]{6})$/';
        $airtel = '/(?:0)?((?:(?:7(?:(?:3[0-9])|(?:5[0-6])|(8[5-9])))|(?:1(?:[0][0-2])))[0-9]{6})$/';
        $telkom = '/(?:0)?(77[0-9][0-9]{6})/';
        $equitel = '/0?(76[3-6][0-9]{6})/';
        if (!$airtime) {
            if (preg_match($safaricom, $mobileNumber)) {
                return 'MPESA';
            } elseif (preg_match($airtel, $mobileNumber)) {
                return 'AIRTELMONEY';
            } elseif (preg_match($telkom, $mobileNumber)) {
                return 'TKASH';
            } elseif (preg_match($equitel, $mobileNumber)) {
                return 'EQUITEL';
            } else {
                return '0';
            }
        } else {
            if (preg_match($safaricom, $mobileNumber)) {
                return 'SAFARICOM';
            } elseif (preg_match($airtel, $mobileNumber)) {
                return 'AIRTEL';
            } elseif (preg_match($telkom, $mobileNumber)) {
                return 'TELKOM';
            } else {
                return '0';
            }
        }
    }

    /**
     * Process payout results.
     *
     * @param Request $request
     *
     * @return TandaTransaction
     */
    public function payout(Request $request): TandaTransaction
    {
        $transaction = TandaTransaction::where('transaction_id', $request->input('transactionId'))->first();

        $transaction->update(
            [
                'json_result' => json_encode($request->all()),
            ]
        );

        if ($request->input('status') == '000000') {
            $transactionReceipt = $request->input('receiptNumber');

            $registeredName = 'N/A';

            if ($request->input('resultParameters')) {
                $params = $request->input('resultParameters');
                $keyValueParams = [];
                foreach ($params as $param) {
                    $keyValueParams[$param['id']] = $param['value'];
                }

                $transactionReceipt = $keyValueParams['transactionRef'];
                if (array_key_exists('accountName', $keyValueParams)) {
                    $registeredName = $keyValueParams['accountName'];
                }
            }

            $data = [
                'request_status'      => $request->input('status'),
                'request_message'     => $request->input('message'),
                'receipt_number'      => $request->input('receiptNumber'),
                'transaction_receipt' => $transactionReceipt,
                'registered_name'     => $registeredName,
                'timestamp'           => $request->input('timestamp'),
            ];
        } else {
            $data = [
                'request_status'  => $request->input('status'),
                'request_message' => $request->input('message'),
                'timestamp'       => $request->input('timestamp'),
            ];
        }

        $transaction->update($data);

        return $transaction;
    }

    /**
     * Process c2b results.
     *
     * @param Request $request
     *
     * @return TandaFunding
     */
    public function c2b(Request $request): TandaFunding
    {
        $funding = TandaFunding::where('transaction_id', $request->input('transactionId'))->first();

        $funding->update(
            [
                'json_result' => json_encode($request->all()),
            ]
        );

        if ($request->input('status') == '000000') {
            $transactionReceipt = $request->input('receiptNumber');

            if ($request->input('resultParameters')) {
                $params = $request->input('resultParameters');
                $keyValueParams = [];
                foreach ($params as $param) {
                    $keyValueParams[$param['id']] = $param['value'];
                }

                $transactionReceipt = $keyValueParams['transactionRef'];
            }

            $data = [
                'request_status'        => $request->input('status'),
                'request_message'       => $request->input('message'),
                'receipt_number'        => $request->input('receiptNumber'),
                'transaction_reference' => $transactionReceipt,
                'timestamp'             => $request->input('timestamp'),
            ];
        } else {
            $data = [
                'request_status'  => $request->input('status'),
                'request_message' => $request->input('message'),
                'timestamp'       => $request->input('timestamp'),
            ];
        }

        $funding->update($data);

        return $funding;
    }

    /**
     * Process ipn results.
     *
     * @param Request $request
     *
     * @return TandaFunding
     */
    public function ipn(Request $request): TandaFunding
    {
        $customer = (object) $request->input('customer');
        $transaction = (object) $request->input('transaction');

        return TandaFunding::create([
            'fund_reference'        => $customer->account,
            'service_provider'      => $transaction->channel,
            'account_number'        => $customer->account,
            'amount'                => intval($transaction->amount),
            'transaction_id'        => $transaction->id,
            'receipt_number'        => $transaction->reference,
            'timestamp'             => date('Y-m-d H:i:s', strtotime($transaction->timestamp)),
            'transaction_reference' => $transaction->reference,
            'json_result'           => json_encode($request->all()),
        ]);
    }

    /**
     * Process transaction p2p results.
     *
     * @param Request $request
     *
     * @return TandaTransaction
     */
    public function p2p(Request $request): TandaTransaction
    {
        $transaction = TandaTransaction::where('payment_reference', $request->input('reference'))->first();

        $transaction->update(
            [
                'json_result' => json_encode($request->all()),
            ]
        );

        if ($request->input('status') == '000000') {
            $transactionReceipt = $request->input('receiptNumber');

            $data = [
                'request_status'      => $request->input('status'),
                'request_message'     => $request->input('message'),
                'receipt_number'      => $request->input('receiptNumber'),
                'transaction_receipt' => $transactionReceipt,
                'timestamp'           => $request->input('timestamp'),
            ];
        } else {
            $data = [
                'request_status'  => $request->input('status'),
                'request_message' => $request->input('message'),
                'timestamp'       => $request->input('timestamp'),
            ];
        }

        $transaction->update($data);

        return $transaction;
    }
}
