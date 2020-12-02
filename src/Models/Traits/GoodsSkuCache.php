<?php

namespace Leady\Goods\Models\Traits;

use Illuminate\Support\Facades\Cache;

trait GoodsSkuCache
{

    /**
     * 商品SKU进入缓存
     */
    public function setSkuCache()
    {
        $cache_name = config('cache.prefix') . $this->id;
        $skus       = $this->skus;
        $stock      = [];
        foreach ($skus as $key => $sku) {
            $stock[$sku->id] = $sku->price->stock;
        }
        $data = [
            'attrs'  => $skus->pluck('sku', 'id')->toArray(),
            'stock'  => $stock,
            'prices' => $this->sku_price->pluck('prices', 'good_sku_id')->toArray(),
        ];
        Cache::put($cache_name, $data);
    }

    /**
     * 获取商品SKU
     * @return array 商品SKU缓存数组
     */
    public function getSkuCache()
    {
        $cache_name = config('cache.prefix') . $this->id;
        if (!Cache::has($cache_name)) {
            self::setSkuCache();
        }

        return Cache::get($cache_name, false);
    }

    /**
     * 扣除缓存中目标SKU的库存
     * @param  int  $good_sku_id 目标SKU的ID
     * @param  int  $number 要扣除的数量
     * @return bool
     */
    public function deductStockCache(int $good_sku_id, int $number)
    {
        $data = self::getSkuCache();
        if ($data['stock'][$good_sku_id] ?? false) {
            $data['stock'][$good_sku_id] -= $number;
            $cache_name                  = config('cache.prefix') . $this->id;
            Cache::put($cache_name, $data);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 增加缓存中目标SKU的库存
     * @Author Leady
     * @param  int  $good_sku_id 目标SKU的ID
     * @param  int  $number 要增加的数量
     * @return bool
     */
    public function increaseStockCache(int $good_sku_id, int $number)
    {
        $data = self::getSkuCache();
        if ($data['stock'][$good_sku_id] ?? false) {
            $data['stock'][$good_sku_id] += $number;
            $cache_name                  = config('cache.prefix') . $this->id;
            Cache::put($cache_name, $data);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 从缓存中获取指定SKU的库存数量
     * @Author Leady
     * @param  int  $good_sku_id 指定SKU的ID
     * @return int|mixed 商品指定SKU库存量
     */
    public function getStockCache(int $good_sku_id)
    {
        $data = self::getSkuCache();
        return $data['stock'][$good_sku_id]??0;
    }

}