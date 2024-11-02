<?php

namespace EdLugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property int id
 * @property int|null funding_id
 * @property string fund_reference
 * @property string service_provider
 * @property string account_number
 * @property string amount
 * @property string|null response_status
 * @property string|null response_message
 * @property string|null transaction_id
 * @property string|null request_status
 * @property string|null request_message
 * @property string|null receipt_number
 * @property string|null timestamp
 * @property string|null transaction_reference
 * @property mixed|null json_result
 * @property mixed|null json_response
 * @property \Illuminate\Support\Carbon|null created_at
 * @property \Illuminate\Support\Carbon|null updated_at
 * @property \Illuminate\Support\Carbon|null deleted_at
 *
 * @method static where(string $string, $input)
 * @method static create(array $array)
 */
class TandaFunding extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
