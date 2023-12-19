<?php

namespace EdLugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TandaFunding extends Model
{
	use SoftDeletes;
	
	protected $guarded = [];
}
