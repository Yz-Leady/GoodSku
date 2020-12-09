<?php

namespace Leady\Goods\Models\Traits;

use Leady\Goods\Models\Goods;

trait MorphManyGoods
{

    public function goods()
    {
        return $this->morphMany(Goods::class, 'store');
    }

}