<?php

namespace Leady\Goods\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Leady\Goods\Models\Goods;

trait BelongsToGood
{

    public function goods():BelongsTo
    {
        return $this->belongsTo(Goods::class)->withTrashed();
    }

}