<?php

namespace Leady\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Leady\Goods\Models\Traits\BelongsToGood;

class GoodsStatusLog extends Model
{
    use BelongsToGood;

    protected $guarded = [];

    const UPDATED_AT = null;

}