<?php

namespace Leady\Goods\Events;

use Leady\Goods\Models\GoodsStatusLog;

/**
 * 商品状态变更记录事件
 * @package Leady\Goods\Events
 */
class GoodsStatusUpdate
{
    public $log;

    public function __construct(GoodsStatusLog $log)
    {
        $this->log = $log;
    }
}