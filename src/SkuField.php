<?php

namespace Leady\Goods;

use Encore\Admin\Admin;
use Encore\Admin\Form\Field;
use Illuminate\Support\Facades\Storage;

class SkuField extends Field
{

    protected $view = 'sku::sku_field';

    public function render()
    {
        $jsurl  = Storage::disk('public')->url('../vendor/yz-leady/goods-sku/sku.js');
        $cssurl = Storage::disk('public')->url('../vendor/yz-leady/goods-sku/sku.css');
        Admin::js($jsurl);
        Admin::css($cssurl);
        $priceArray   = json_encode(config("yzgoods.prices"));
        $this->script = <<< EOF
        var priceArray=JSON.parse('{$priceArray}');
window.DemoSku = new JadeKunSKU('{$this->getElementClassSelector()}',priceArray)
EOF;

        return parent::render();
    }

}
