<?php

namespace EdLugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int|null payment_id
 * @property string payment_reference
 * @property string service_provider
 * @property string|null  merchant_wallet
 * @property string amount
 * @property string|null account_number
 * @property string|null contact
 * @property string|null service_provider_id
 * @property string|null response_status
 * @property string|null response_message
 * @property string|null transaction_id
 * @property string|null request_status
 * @property string|null request_message
 * @property string|null receipt_number
 * @property string|null transaction_receipt
 * @property string|null timestamp
 * @property string transactable_type
 * @property int transactable_id
 * @property mixed|null  json_response
 * @property mixed|null  json_result
 * @property mixed|null  json_request
 * @property string|null registered_name
 * @property \Illuminate\Support\Carbon|null created_at
 * @property \Illuminate\Support\Carbon|null updated_at
 * @property \Illuminate\Support\Carbon|null deleted_at
 *
 * @method static create(array|string[] $array_merge)
 * @method update(array $array)
 * @method static where(string $string, $input)
 */
class TandaTransaction extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
