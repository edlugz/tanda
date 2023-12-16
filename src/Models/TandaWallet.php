<?php

namespace Edlugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TandaWallet extends Model
{
	use SoftDeletes;
	
	protected $guarded = [];
}
