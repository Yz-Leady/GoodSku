<?php

namespace Leady\Goods\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Leady\Goods\Models\Good;

class GoodsController extends AdminController
{

    protected $title = '商品管理';

    public function grid()
    {
        $grid = new Grid(new Good());
        $grid->column('ID', 'id');
        $grid->column('cover', '展示图');
        $grid->column('title', '标题');
        $grid->column('status', '状态')
             ->using(Good::STATUS_ARRAY)
             ->label([
                 Good::STATUS_INIT    => 'default',
                 Good::STATUS_SUCCESS => 'primary',
                 Good::STATUS_NORMAL  => 'success',
                 Good::STATUS_REJECT  => 'danger',
                 Good::STATUS_SHELVES => 'warning',
             ]);
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        return $grid;
    }

    public function form()
    {
        $form = new Form(new Good());
        $form->tab('商品基本信息', function (Form $form) {
            $form->select('store_type', '归属类型')
                 ->options(config('yzgoods.store_list'))
                 ->load('store_id', config('yzgoods.ajaxs.stores'));
            $form->select('store_id', '归属')
                 ->options([
                     '0' => '系统',
                 ])
                 ->required();
            $form->text('title', '商品标题');
            $form->select('category_id', '商品分类')
                 ->ajax(config('yzgoods.ajaxs.category'))
                 ->required();
            $form->image('cover', '商品展示图')
                 ->move(config('yzgoods.images.path') . date('Y/m/d'))
                 ->removable()
                 ->uniqueName();
            $form->multipleImage('pictures', '轮播图')
                 ->move(config('yzgoods.images.path') . date('Y/m/d'))
                 ->removable()
                 ->uniqueName();
            $form->textarea('description', '商品描述');
            $form->ueditor('content', '商品描述');
            $form->radioButton('status', '状态')->options(Good::STATUS_ARRAY);
        });

        $form->tab('参数配置', function (Form $form) {

        });

        return $form;
    }

}