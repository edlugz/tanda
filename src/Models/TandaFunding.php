<?php

namespace EdLugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $string, $input)
 * @method static create(array $array)
 */
class TandaFunding extends Model
{
	use SoftDeletes;
	
	protected $guarded = [];
}
