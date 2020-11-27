<?php
return [
    //拥有商品的模型列表 User::class=>'用户'
    'store_list' => [
    ],

    //价格体系
    'prices'     => [
        'cast'    => '成本',
        'price'   => '售价',
        'vip'     => '会员价',
        'welfare' => '福利价',
    ],
    //图片配置
    'images'     => [
        'path' => 'images/',
    ],
    //后台获取内容的地址
    'ajaxs'      => [
        'stores'   => '',//根据商品拥有者模型获取标题与ID
        'category' => '',//分类获取路由
    ],
    //后台管理路由
    'routers'    => [
        'goods' => 'goods',
    ],
];