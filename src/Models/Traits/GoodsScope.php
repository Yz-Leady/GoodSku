<?php

namespace Leady\Goods\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Leady\Goods\Models\Goods;

trait GoodsScope
{
    /**
     * 返回上架商品
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNormal(Builder $query): Builder
    {
        return $query->where('status', Goods::STATUS_NORMAL);
    }

    /**
     * 返回已下架商品
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShelves(Builder $query):Builder
    {
        return $query->where('status',Goods::STATUS_SHELVES);
    }

    /**
     * 返回已审核商品
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccess(Builder $query):Builder
    {
        return $query->where('status',Goods::STATUS_SUCCESS);
    }

    /**
     * 返回已驳回商品
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReject(Builder $query):Builder
    {
        return $query->where('status',Goods::STATUS_REJECT);
    }

}