<?php

namespace Leady\Goods\Models\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Leady\Goods\Models\GoodsSkuPrice;

trait GoodsAttribute
{
    public function getSkuAttribute()
    {
        $data        = [];
        $prices      = GoodsSkuPrice::whereIn('goods_sku_id', $this->skus()->pluck('id')->toArray())
                                    ->get();
        $data['sku'] = [
            "type"  => "many",
            "attrs" => $this->configs->configs['attrs'],
            "sku"   => $prices->pluck('prices'),
        ];

        $this->setAttribute('sku', $data);
        $this->attributes['sku'] = $data;

        return $data;
    }

    /**
     * Notes: 封面图片网络地址转换
     * @Author: <C.Jason>
     * @Date  : 2020/9/1 4:53 下午
     * @return string
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover) {
            return Storage::url($this->cover);
        } else {
            return '';
        }
    }

    /**
     * Notes: 多图网络地址转换
     * @Author: <C.Jason>
     * @Date  : 2020/9/1 4:53 下午
     * @return \Illuminate\Support\Collection
     */
    protected function getPicturesUrlAttribute(): Collection
    {
        return collect($this->pictures)->map(function ($pic) {
            return Storage::url($pic);
        });
    }
}