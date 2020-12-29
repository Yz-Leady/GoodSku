<?php

namespace Leady\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Leady\Goods\Models\Traits\BelongsToGood;

class GoodsSkuPrice extends Model
{

    use BelongsToGood;

    protected $guarded = [];

    protected $casts   = [
        'prices' => 'json',
    ];

    public function sku(): BelongsTo
    {
        return $this->belongsTo(GoodsSku::class);
    }

}
