<?php

namespace Leady\Goods\Events;

use Leady\Goods\Models\Goods;

/**
 * 商品驳回
 * @package Leady\Goods\Events
 */
class GoodsAuditReject
{

    public $goods;

    public function __construct(Goods $goods)
    {
        $this->goods = $goods;
    }
}