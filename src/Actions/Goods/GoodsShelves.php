<?php

namespace Leady\Goods\Actions\Goods;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class GoodsShelves extends RowAction
{
    public $name = '下架';

    public function handle(Model $model)
    {
        try {
            $model->status_shelves();
            return $this->response()->success('操作成功')->refresh();
        } catch (\Exception $e) {
            return $this->response()->error('操作失败')->refresh();
        }
    }

    public function dialog()
    {
        $this->confirm('是否确认上架商品？');
    }
}