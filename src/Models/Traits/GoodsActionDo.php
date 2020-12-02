<?php

namespace Leady\Goods\Models\Traits;

use Leady\Goods\Events\GoodsAuditReject;
use Leady\Goods\Events\GoodsAuditSuccess;
use Leady\Goods\Events\GoodsStatusNormal;
use Leady\Goods\Events\GoodsStatusUpdate;
use Leady\Goods\Models\Goods;
use Leady\Goods\Models\GoodsSku;

trait GoodsActionDo
{

    /**
     * 商品状态变更记录
     * @author Leady
     * @param  int     $befor 变更前状态
     * @param  int     $after 变更后状态
     * @param  string  $remark 备注
     * @return mixed
     */
    public function start_log(int $befor, int $after, string $remark)
    {
        $log = $this->statuslog()->create([
            'befor'  => $befor,
            'after'  => $after,
            'remark' => $remark,
        ]);
        event(new GoodsStatusUpdate($log));

        return $log;
    }

    /**
     * 审核通过
     * @author Leady
     * @param  string  $remark
     * @return bool
     */
    public function audit_success($remark = '审核通过')
    {
        $befor        = $this->status;
        $this->status = $after = Goods::STATUS_SUCCESS;
        try {
            $this->save();
            self::start_log($befor, $after, $remark);
            event(new GoodsAuditSuccess($this));

            return true;
        } catch (\Exception $e) {
            self::start_log($befor, $after, $e->getMessage());

            return false;
        }
    }

    /**
     * 驳回
     * @author Leady
     * @param  string  $remark
     * @return bool
     */
    public function audit_reject($remark = '')
    {
        $befor        = $this->status;
        $this->status = $after = Goods::STATUS_REJECT;
        try {
            $this->save();
            self::start_log($befor, $after, $remark);
            event(new GoodsAuditReject($this));

            return true;
        } catch (\Exception $e) {
            self::start_log($befor, $after, $e->getMessage());

            return false;
        }
    }

    /**
     * 商品上架
     * @author Leady
     * @return bool
     */
    public function status_normal()
    {
        $befor        = $this->status;
        $this->status = $after = Goods::STATUS_NORMAL;
        try {
            $this->save();
            self::start_log($befor, $after, '商品上架');
            event(new GoodsStatusNormal($this));

            return true;
        } catch (\Exception $e) {
            self::start_log($befor, $after, $e->getMessage());

            return false;
        }
    }

    /**
     * 商品下架
     * @author Leady
     * @return bool
     */
    public function status_shelves()
    {
        $befor        = $this->status;
        $this->status = $after = Goods::STATUS_SHELVES;
        try {
            $this->save();
            self::start_log($befor, $after, '商品下架');
            event(new GoodsStatusShelves($this));

            return true;
        } catch (\Exception $e) {
            self::start_log($befor, $after, $e->getMessage());

            return false;
        }
    }

    /**
     * 反馈商品SKU对应价格
     * @author Leady
     * @param  array  $array  商品SKU参数
     * @return \Leady\Goods\Models\GoodsSku 商品SKU模型
     */
    public function getSku($array = []): GoodsSku
    {
        $model = $this->skus();
        foreach ($array as $k => $v) {
            $model->where('sku->' . $k, $v);
        }

        return $model->first();
    }

    /**
     * 扣除商品摸一个SKU的库存
     * @author Leady
     * @param  array|\Leady\Goods\Models\GoodsSku  $model   array:商品SKU属性,Model:GoodSku模型
     * @param  int                                 $number  扣除数量
     * @return bool
     */
    public function stock_deduct($model, $number = 1)
    {
        if (is_array($model)) {
            $model = self::getSku($model);
        }
        if (get_class($model) != GoodsSku::class) {
            return false;
        }
        try {
            $model->price()->decrement('stock', $number);
            self::deductStockCache($model->id, $number);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 增加商品摸一个SKU的库存
     * @author Leady
     * @param  array|\Leady\Goods\Models\GoodsSku  $model   array:商品SKU属性,Model:GoodSku模型
     * @param  int                                 $number  增加数量
     * @return bool
     */
    public function stock_increase($model, $number = 1)
    {
        if (is_array($model)) {
            $model = self::getSku($model);
        }
        if (get_class($model) != GoodsSku::class) {
            return false;
        }
        try {
            $model->price()->increment('stock', $number);
            self::increaseStockCache($model->id, $number);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}