<?php

namespace EdLugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property  int id
 * @property string|null wallet_id
 * @property int account_id
 * @property string name
 * @property string|null wallet_account_number
 * @property float actual_balance
 * @property float available_balance
 * @property string ipnUrl
 * @property string username
 * @property string password
 * @property \Illuminate\Support\Carbon|null created_at
 * @property \Illuminate\Support\Carbon|null updated_at
 * @property \Illuminate\Support\Carbon|null deleted_at
 *
 */
class TandaWallet extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
