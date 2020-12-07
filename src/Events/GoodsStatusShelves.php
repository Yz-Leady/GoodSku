<?php

namespace Leady\Goods\Events;

use Leady\Goods\Models\Goods;

/**
 * 商品下架事件
 * @package Leady\Goods\Events
 */
class GoodsStatusShelves
{
    public $goods;

    public function __construct(Goods $goods)
    {
        $this->goods = $goods;
    }
}