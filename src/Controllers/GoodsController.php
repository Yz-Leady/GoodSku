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
                     ->load('store_id', route(config('yzgoods.ajaxs.stores')))
                     ->required();
            } else {
                $form->select('store_type', '归属类型')
                     ->options(config('yzgoods.store_list'))->required();
            }
            $form->select('store_id', '归属')
                 ->options([
                     '0' => '系统',
                 ])
                 ->load('category_id', route(config('yzgoods.ajaxs.category')))
                 ->required();
            $form->text('title', '商品标题')->required();
            $form->select('category_id', '商品分类');
            $form->image('cover', '商品展示图')
                 ->move(config('yzgoods.images.path') . date('Y/m/d'))
                 ->removable()
                 ->uniqueName();
            $form->multipleImage('pictures', '轮播图')
                 ->move(config('yzgoods.images.path') . date('Y/m/d'))
                 ->removable()
                 ->uniqueName();
            $form->textarea('description', '商品描述')->required();
            $editor = config('yzgoods.editor');
            $form->$editor('content', '商品内容');
            $form->radioButton('status', '状态')->options(Goods::STATUS_ARRAY);
        });

        $form->tab('参数配置', function (Form $form) {
            $form->radio('configs.showtype', '商品模式')
                 ->options([
                     'normal' => '正常',
                     'story'  => '宣传商品',
                 ])->default('normal')
                 ->required();
            $states = [
                'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
            ];
            $form->radio('configs.stock_type', '扣库存方式')
                 ->options(Goods::STOCK_ARRAY)
                 ->default(Goods::STOCK_PAID);
            $form->switch('configs.is_push', '是否推荐')
                 ->states($states)
                 ->default(0);
            $form->number('configs.push_order', '推荐排序')
                 ->default(0)
                 ->min(0)
                 ->max(99);
            $form->radio('configs.is_preferential', '是否特卖')
                 ->options([
                     0 => '否',
                     1 => '是',
                 ])
                 ->when(1, function ($form) {
                     $form->datetime('configs.preferential_start', '开始时间');
                     $form->datetime('configs.preferential_end', '结束时间');
                     $form->rate('configs.preferential_rate', '优惠比例');
                 })
                 ->default(0);

            $form->radio('configs.is_limit', '是否限购')->options([
                0 => '无',
                1 => '每天',
                2 => '总限',
            ])->default(0);
            $form->number('configs.limit_number', '限购数量')
                 ->default(0)
                 ->min(0)
                 ->max(99);
            $form->radio('configs.freight_type', '运费模式')
                 ->options(Goods::FREIGHT_ARRAY)
                 ->when(Goods::FREIGHT_SINGLE, function ($form) {
                     $form->currency('configs.freight_single', '单件运费金额')
                          ->default(0);
                 })->default(Goods::FREIGHT_UNIFIED);
        });
        $form->tab('商品规格', function (Form $form) use ($good) {
            $def = [
                'type'  => 'many',
                'attrs' => [],
                'sku'   => [],
            ];
            $form->sku('sku.sku', '商品规格')->default($good->sku['sku'] ?? $def);
        });
        $form->submitted(function (Form $form) {
            $skus = json_decode($form->sku, true);
            if (count($skus['attrs']) <= 0 || count($skus['sku']) <= 0) {
                throw new \Exception('请填写规格配置');
            }
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
                $rowkey = implode('|', array_values($attr));

                $sku = $good->skus()->updateOrCreate([
                    'rowkey' => $rowkey,
                ], [
                    'sku' => $attr,
                ]);

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
            $good->refresh()->setSkuCache();
        });

        return $form;
    }

}
