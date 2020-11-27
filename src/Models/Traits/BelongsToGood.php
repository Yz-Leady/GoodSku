<?php

namespace Leady\Goods\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Leady\Goods\Models\Good;

trait BelongsToGood
{

    public function good():BelongsTo
    {
        return $this->belongsTo(Good::class);
    }

}