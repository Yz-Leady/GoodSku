<?php

namespace Leady\Goods\Models\Traits;

trait GoodsHasOther
{

    public function coupon()
    {
        if (config('yzgoods.relationship.coupon')) {
            switch (config('yzgoods.relationship.coupon.type')) {
                case 'hasOne':
                    return $this->hasOne(config('yzgoods.relationship.coupon.class'),
                        config('yzgoods.relationship.coupon.foreignKey'));
                    break;
                case 'hasMany':
                    return $this->hasMany(config('yzgoods.relationship.coupon.class'),
                        config('yzgoods.relationship.coupon.foreignKey'));
                    break;
                case 'morphTo':
                    return $this->morphTo(config('yzgoods.relationship.coupon.class'),
                        config('yzgoods.relationship.coupon.foreignKey'));
                    break;
                case 'morphMany':
                    return $this->morphMany(config('yzgoods.relationship.coupon.class'),
                        config('yzgoods.relationship.coupon.foreignKey'));
                    break;
            }
        } else {
            return null;
        }
    }

}