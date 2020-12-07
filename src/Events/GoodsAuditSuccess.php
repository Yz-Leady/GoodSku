<?php

namespace Leady\Goods\Events;

use Leady\Goods\Models\Goods;

/**
 * 商品审核通过
 * @package Leady\Goods\Events
 */
class GoodsAuditSuccess
{
    public $goods;

    public function __construct(Goods $goods)
    {
        $this->goods = $goods;
    }
}