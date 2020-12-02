<?php

namespace Leady\Goods\Models\Traits;

use Leady\Goods\Models\Goods;

trait GoodsCanDo
{

    /**
     * 商品是否可以审核
     * @return bool
     */
    public function canAudit()
    {
        return $this->status == Goods::STATUS_INIT;
    }

    /**
     * 商品是否可以上架
     * @return bool
     */
    public function canNormal()
    {
        return in_array($this->status, [
            Goods::STATUS_SUCCESS,
            Goods::STATUS_SHELVES,
        ]);
    }

    /**
     * 商品是否可以下架
     * @return bool
     */
    public function canShelves()
    {
        return $this->status == Goods::STATUS_NORMAL;
    }

    /**
     * 商品是否可以购买
     * @return bool
     */
    public function canBuy()
    {
        return $this->status == Goods::STATUS_NORMAL;
    }

}