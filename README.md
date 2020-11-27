# GoodSku
>基于Laravel-Admin后端，商品管理集成SKU体系。
## 1.安装
```shell script
$ composer require yz-leady/goods-sku
```
## 2.初始化
##### *先做好相关的配置项
```shell script
php artisan vendor:publish --provider="Leady\Goods\ServiceProvider"

php artisan migrate
```

