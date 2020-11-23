<?php

namespace Leady\Goods;

use Encore\Admin\Form\Field;

class SkuField extends Field
{
    protected $view = 'sku::sku_field';

    protected static $js = [
        'vendor/yz-leady/goods-sku/sku.js'
    ];

    protected static $css = [
        'vendor/yz-leady/goods-sku/sku.css'
    ];

    public function render()
    {

        $this->script = <<< EOF
window.DemoSku = new JadeKunSKU('{$this->getElementClassSelector()}')
EOF;
        return parent::render();
    }
}