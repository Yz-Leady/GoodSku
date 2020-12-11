<?php

namespace Leady\Goods\Models\Traits;

trait GoodsHasOther
{

    public function coupon()
    {
        $configs = config('yzgoods.relationship.coupon');
        $type    = $configs['type'];

        return $this->$type($configs['class'], $configs['foreignKey']);
    }

}