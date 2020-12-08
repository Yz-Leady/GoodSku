<?php

namespace Leady\Goods\Models;

use EasyWeChat\Kernel\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Leady\Goods\Models\Traits\GoodsActionDo;
use Leady\Goods\Models\Traits\GoodsCanDo;
use Leady\Goods\Models\Traits\GoodsSkuCache;
use Leady\Goods\Models\Traits\GoodsScope;

class Goods extends Model
{

    use SoftDeletes,
        GoodsActionDo,
        GoodsSkuCache,
        GoodsScope,
        GoodsCanDo;

    public    $sku_attrs;

    public    $sku_config_attrs;

    public    $sku_prices;

    protected $guarded = [];

    protected $casts   = [
        'pictures' => 'json',
    ];

    const STATUS_INIT    = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_NORMAL  = 2;
    const STATUS_REJECT  = 3;
    const STATUS_SHELVES = 4;
    const STATUS_FORCE   = 99;

    const STATUS_ARRAY = [
        self::STATUS_INIT    => '待审核',
        self::STATUS_SUCCESS => '已审核',
        self::STATUS_NORMAL  => '上架',
        self::STATUS_REJECT  => '已驳回',
        self::STATUS_SHELVES => '下架',
        self::STATUS_FORCE   => '永久删除',
    ];

    const STOCK_PAID    = 0;
    const STOCK_CONFIRM = 1;

    const STOCK_ARRAY = [
        self::STOCK_PAID    => '支付扣库存',
        self::STOCK_CONFIRM => '订单扣库存',
    ];

    const FREIGHT_SINGLE  = 1;
    const FREIGHT_UNIFIED = 3;

    const FREIGHT_ARRAY = [
        self::FREIGHT_SINGLE  => '单件计费',
        self::FREIGHT_UNIFIED => '计重运费',
    ];

    protected static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            $model->start_log(0, self::STATUS_INIT, '新增');
            $model->setSkuCache();
        });
        self::deleting(function ($model) {
            if ($model->forceDeleting) {
                //永久删除记录
                $this->start_log($model->status, self::STATUS_FORCE, '永久删除');
            }
        });
        self::forceDeleted(function ($model) {
            //商品永久删除时，删除如下关联
            $model->configs()->delete();
            $model->skus()->delete();
            $model->sku_price()->delete();
        });

    }

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

    public function setSkuAttrsAttribute($value)
    {
        $this->sku_attrs = $value;
    }

    public function setSkuConfigAttrsAttribute($value)
    {
        $this->sku_config_attrs = $value;
    }

    public function setSkuPricesAttribute($value)
    {
        $this->sku_prices = $value;
    }

    /**
     * @param  array  $attrs
     * @param  array  $data
     * @return array
     */
    public static function assemblySku(array $attrs, array $datas): array
    {
        $cuteNumber = count($attrs);
        $attr       = $price = [];
        foreach ($datas as $key => $data) {
            $attr[]  = array_slice($data, 0, $cuteNumber, true);
            $price[] = $data;
        }

        return [$attr, $price];
    }

    /**
     * 返回状态文字
     * @return string
     */
    protected function getStatusTextAttribute()
    {
        return self::STATUS_ARRAY[$this->status] ?? '无';
    }

    /**
     * 关联多条SKU
     * @return mixed
     */
    public function skus()
    {
        return $this->hasMany(GoodsSku::class);
    }

    /**
     * 关联商品配置
     * @return mixed
     */
    public function configs()
    {
        return $this->hasOne(GoodsConfig::class);
    }

    public function sku_price()
    {
        return $this->hasMany(GoodsSkuPrice::class);
    }

    /**
     * 关联商品状态变更
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuslog()
    {
        return $this->hasMany(GoodsStatusLog::class);
    }

}
