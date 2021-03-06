<?php

namespace Leady\Goods\Events;

use Leady\Goods\Models\Goods;

/**
 * 商品上架事件
 * @package Leady\Goods\Events
 */
class GoodsStatusNormal
{
    public $goods;

    public function __construct(Goods $goods)
    {
        $this->goods = $goods;
    }
}