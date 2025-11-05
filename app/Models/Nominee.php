<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nominee extends Model
{
    use SoftDeletes;

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
