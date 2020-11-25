<?php

namespace Leady\Goods;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * 部署时加载
     * @Author:<Leady>
     * @Date:2020-11-20T12:30:20+0800
     * @return void
     */
    public function boot(Sku $extension)
    {
        if (! Sku::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'sku');
        }

        if ($this->app->runningInConsole()) {
            if($assets = $extension->assets()){
                $this->publishes(
                    [$assets => public_path('vendor/yz-leady/goods-sku')],
                    'sku'
                );
            }
            $this->publishes([__DIR__ . '/../config/config.php' => config_path('yzgoods.php')], 'yzgoods');
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        }

        Admin::booting(function () {
            Form::extend('sku', SkuField::class);
        });

        Route::group([
            'prefix'     => config('admin.route.prefix'),
            'namespace'  => 'Leady\Goods\Controllers',
            'middleware' => config('admin.route.middleware'),
        ], function (Router $router) {
            $router->resource(config('yzgoods.routers.goods'), 'GoodsController');
        });
    }

    /**
     * 部署时加载
     * @Author:<Leady>
     * @Date:2020-11-20T12:30:20+0800
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'yzgoods');
    }
}