<?php

namespace Leady\Goods\Models;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{

    protected $guarded = [];

    /**
     * 关联SKU

     * @return mixed
     */
    public function sku()
    {
        return $this->hasOne(GoodSku::class);
    }

    /**
     * 关联商品配置
     * @return mixed
     */
    public function configs()
    {
        return $this->hasOne(GoodConfig::class);
    }

}