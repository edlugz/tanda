<?php

namespace EdLugz\Tanda\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(array|string[] $array_merge)
 * @method update(array $array)
 * @method static where(string $string, $input)
 */
class TandaTransaction extends Model
{
    use SoftDeletes;

    protected $guarded = [];
}
