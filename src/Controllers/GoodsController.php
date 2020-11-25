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
                 self::STATUS_INIT    => 'default',
                 self::STATUS_SUCCESS => 'primary',
                 self::STATUS_NORMAL  => 'success',
                 self::STATUS_REJECT  => 'danger',
                 self::STATUS_SHELVES => 'warning',
             ]);
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        return $grid;
    }

    public function form()
    {
        $form = new Form(new Good());

        return $form;
    }

}