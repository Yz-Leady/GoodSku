<?php

namespace Leady\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Leady\Goods\Models\Traits\BelongsToGood;

class GoodSku extends Model
{
    use BelongsToGood;

    protected $guarded = [];

    protected $casts = [
        'sku'   => 'json',
        'other' => 'json',
    ];

    /**
     * 价格关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function price(): HasOne
    {
        return $this->hasOne(GoodSkuPrice::class);
    }
}
