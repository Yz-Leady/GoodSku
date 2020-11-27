<?php

namespace Leady\Goods\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{

    protected $guarded = [];

    const STATUS_INIT    = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_NORMAL  = 2;
    const STATUS_REJECT  = 3;
    const STATUS_SHELVES = 4;

    const STATUS_ARRAY = [
        self::STATUS_INIT    => '待审核',
        self::STATUS_SUCCESS => '已审核',
        self::STATUS_NORMAL  => '上架',
        self::STATUS_REJECT  => '已驳回',
        self::STATUS_SHELVES => '下架',
    ];

    const STOCK_PAID    = 0;
    const STOCK_CONFIRM = 1;

    const STOCK_ARRAY = [
        self::STOCK_PAID    => '支付扣库存',
        self::STOCK_CONFIRM => '订单扣库存',
    ];

    /**
     * 返回上架商品
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNormal(Builder $query):Builder
    {
        return $query->where('status',self::STATUS_NORMAL);
    }


    /**
     * 返回状态文字
     * @return string
     */
    protected function getStatusTextAttribute()
    {
        return self::STATUS_ARRAY[$this->status] ?? '无';
    }

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