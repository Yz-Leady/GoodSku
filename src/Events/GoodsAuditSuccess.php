<?php

namespace Leady\Goods\Events;

use Leady\Goods\Models\Goods;

/**
 * 商品审核通过
 * @package Leady\Goods\Events
 */
class GoodsAuditSuccess
{
    public $good;

    public function __construct(Goods $good)
    {
        $this->good = $good;
    }
}