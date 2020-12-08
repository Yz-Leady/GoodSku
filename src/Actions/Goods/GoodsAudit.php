<?php

namespace Leady\Goods\Actions\Goods;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Leady\Goods\Models\Goods;

class GoodsAudit extends RowAction
{

    public $name = '审核操作';

    public function handle(Model $model, Request $request)
    {
        try {
            if ($request->status == Goods::STATUS_SUCCESS) {
                $model->audit_success($request->remark?:'');
            } elseif ($request->status == Goods::STATUS_REJECT) {
                $model->audit_reject($request->remark);
            }

            return $this->response()->success('操作成功')->refresh();
        } catch (\Exception $e) {
            return $this->response()->error('操作失败')->refresh();
        }
    }

    public function form(Model $model)
    {
        $this->text('商品名称')->value($model->title)->disable();
        $this->select('status', '审核操作')
             ->options([
                 Goods::STATUS_SUCCESS => '通过',
                 Goods::STATUS_REJECT  => '驳回',
             ])
             ->required();
        $this->textarea('remark', '说明');

    }

}