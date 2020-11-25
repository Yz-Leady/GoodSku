<?php

namespace Leady\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Leady\Goods\Models\Traits\BelongsToGood;

class GoodSku extends Model
{
    use BelongsToGood;

    protected $guarded = [];

    protected $casts = [
        'sku' => 'json',
        'other' => 'json',
    ];
}