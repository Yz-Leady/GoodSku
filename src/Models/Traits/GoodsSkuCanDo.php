<?php

namespace Leady\Goods\Models\Traits;

trait GoodsSkuCanDo
{

    /**
     * 商品指定SKU是否可购买
     * @Author Leady
     * @param  int  $number  购买数量默认0
     * @return bool
     */
    public function canBuy($number = 0)
    {
        if ($this->good->canBuy()) {
            return false;
        }
        $stock = $this->good->getStockCache($this->id);
        if ($stock <= $number) {
            return false;
        }

        return true;
    }

}