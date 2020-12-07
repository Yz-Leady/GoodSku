<?php

namespace Leady\Goods\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Leady\Goods\Actions\Goods\GoodsAudit;
use Leady\Goods\Actions\Goods\GoodsNormal;
use Leady\Goods\Actions\Goods\GoodsShelves;
use Leady\Goods\Models\Goods;

class GoodsController extends AdminController
{

    protected $title = '商品管理';

    public function grid()
    {
        $grid = new Grid(new Goods());
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->row->canAudit()) {
                $actions->add(new GoodsAudit());
            }
            if ($actions->row->canNormal()) {
                $actions->add(new GoodsNormal());
            }
            if ($actions->row->canShelves()) {
                $actions->add(new GoodsShelves());
            }
            $actions->disableView();
        });
        $grid->column('ID', 'id');
        $grid->column('cover', '展示图');
        $grid->column('title', '标题');
        $grid->column('status', '状态')
             ->using(Goods::STATUS_ARRAY)
             ->label([
                 Goods::STATUS_INIT    => 'default',
                 Goods::STATUS_SUCCESS => 'primary',
                 Goods::STATUS_NORMAL  => 'success',
                 Goods::STATUS_REJECT  => 'danger',
                 Goods::STATUS_SHELVES => 'warning',
             ]);
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '更新时间');

        return $grid;
    }

    public function edit($id, Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($this->form($id)->edit($id));
    }

    public function form($id = '')
    {
        $good = Goods::find($id);
        $form = new Form(new Goods());
        $form->tab('商品基本信息', function (Form $form) {
            if (config('yzgoods.ajaxs.stores')) {
                $form->select('store_type', '归属类型')
                     ->options(config('yzgoods.store_list'))
                     ->load('store_id', route(config('yzgoods.ajaxs.stores')));
            } else {
                $form->select('store_type', '归属类型')
                     ->options(config('yzgoods.store_list'));
            }
            $form->select('store_id', '归属')
                 ->options([
                     '0' => '系统',
                 ])
                 ->load('category_id', route(config('yzgoods.ajaxs.category')))
                 ->required();
            $form->text('title', '商品标题');
            $form->select('category_id', '商品分类');
            $form->image('cover', '商品展示图')
                 ->move(config('yzgoods.images.path') . date('Y/m/d'))
                 ->removable()
                 ->uniqueName();
            $form->multipleImage('pictures', '轮播图')
                 ->move(config('yzgoods.images.path') . date('Y/m/d'))
                 ->removable()
                 ->uniqueName();
            $form->textarea('description', '商品描述');
            $editor = config('yzgoods.editor');
            $form->$editor('content', '商品描述');
            $form->radioButton('status', '状态')->options(Goods::STATUS_ARRAY);
        });

        $form->tab('参数配置', function (Form $form) {
            $states = [
                'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
            ];
            $form->radio('configs.configs.stock_type', '扣库存方式')
                 ->options(Goods::STOCK_ARRAY);
            $form->switch('configs.configs.is_push', '是否推荐')->states($states);
            $form->number('configs.configs.push_order', '推荐排序')
                 ->default(0)
                 ->min(0)
                 ->max(99);
            $form->radio('configs.configs.is_limit', '是否限购')->options([
                0 => '无',
                1 => '每天',
                2 => '总限',
            ]);
            $form->number('configs.configs.limit_number', '限购数量')
                 ->default(0)
                 ->min(0)
                 ->max(99);
            $form->radio('configs.configs.freight_type', '运费模式')
                 ->options(Goods::FREIGHT_ARRAY)
                 ->when(Goods::FREIGHT_SINGLE, function ($form) {
                     $form->currency('configs.configs.freight_single', '单件运费金额')
                          ->default(0);
                 });
        });
        $form->tab('商品规格', function (Form $form) use ($good) {
            $def = [
                'type'  => 'many',
                'attrs' => [],
                'sku'   => [],
            ];
            $form->sku('sku.sku', '商品规格')->default($good->sku['sku'] ?? $def);
        });

        $form->saving(function (Form $form) {
            $sku_array = $form->sku;
            $skus      = $sku_array['sku'];
            $skus      = json_decode($skus, true);
            [$sku_attrs, $sku_prices] = Goods::assemblySku($skus['attrs'], $skus['sku']);
            $form->model()->sku_attrs        = $sku_attrs;
            $form->model()->sku_config_attrs = $skus['attrs'];
            $form->model()->sku_prices       = $sku_prices;
        });

        $form->saved(function (Form $form) {
            $good             = $form->model()->refresh();
            $configs          = $good->configs->configs;
            $configs['attrs'] = $good->sku_config_attrs;
            $good->configs()->update([
                'configs' => $configs,
            ]);
            $ids = [];
            foreach ($good->sku_attrs as $key => $attr) {
                $skuWhere = [];
                foreach ($attr as $k => $v) {
                    $skuWhere['sku->' . $k] = $v;
                }
                $sku = $good->skus()->updateOrCreate($skuWhere);
                if ($sku) {
                    $price = $good->sku_prices[$key];
                    $sku->price()->updateOrCreate([
                        'goods_id' => $good->id,
                    ], [
                        'prices' => $price,
                        'stock'  => $price['stock'],
                    ]);
                }
                $ids[] = $sku->id;
            }
            $good->skus()->whereNotIn('id', $ids)->forceDelete();
            $good->sku_price()->whereNotIn('goods_sku_id', $ids)->forceDelete();
        });

        return $form;
    }

}
