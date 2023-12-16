<?php

namespace Edlugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TandaTransaction extends Model
{
	use SoftDeletes;
	
	protected $guarded = [];
}
