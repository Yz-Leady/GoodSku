<?php

namespace Leady\Goods\Models\Traits;

use Leady\Goods\Models\Good;

trait BelongsToGood
{

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

}