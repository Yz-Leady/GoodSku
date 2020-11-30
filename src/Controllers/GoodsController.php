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
                 ->required();
            $form->text('title', '商品标题');
            $form->select('category_id', '商品分类')
                 ->ajax(config('yzgoods.ajaxs.category'));
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
            $form->radioButton('status', '状态')->options(Good::STATUS_ARRAY);
        });

        $form->tab('参数配置', function (Form $form) {
            $states = [
                'on'  => ['value' => 1, 'text' => '是', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '否', 'color' => 'danger'],
            ];
            $form->radio('configs.configs.stock_type', '扣库存方式')
                 ->options(Good::STOCK_ARRAY);
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
        });

        $form->tab('价格配置', function (Form $form) {
            $form->html('此价格只在单规格时生效');
            foreach (config('yzgoods.prices') as $key => $value) {
                $form->currency('sku.prices.' . $key, $value);
            }
            $form->number('sku.prices.stock', '库存')->min(0);
        });

        $form->tab('商品规格', function (Form $form) {
            $form->sku('sku.sku', '商品规格')->default(['']);
        });

        $form->saving(function (Form $form) {
            $sku_array              = $form->sku;
            $form->configs['attrs'] = $sku_array['sku']['attrs'] ?? [];
            $skus                   = $sku_array['sku'];
            $prices                 = $sku_array['prices'];
            $skus                   = json_decode($skus, true);
            $types                  = $skus['type'] ?? 'single';
            if ($types == 'many') {
                [$sku_attrs, $sku_prices] = Good::assemblySku($skus['attrs'], $skus['sku']);
            } else {
                $sku_attrs  = $types;
                $sku_prices = $prices;
            }
            $form->model()->sku_attrs  = $sku_attrs;
            $form->model()->sku_prices = $sku_prices;
            $form->sku_type            = $types;
        });

        $form->saved(function (Form $form) {
            $good = $form->model();
            $good->skus()->whereNotIn('sku', $good->sku_attrs)->delete();
            foreach ($good->sku_attrs as $key => $attr) {
                $sku = $good->skus()->updateOrCreate([
                    'sku' => $attr,
                ]);
                if ($sku) {
                    $price = $good->sku_prices[$key];
                    $sku->price()->updateOrCreate([
                        'good_id' => $good->id,
                    ], [
                        'prices' => $price,
                        'stock'  => $price['stock'],
                    ]);
                }
            }
        });

        return $form;
    }

}
