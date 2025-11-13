<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperAwardFeedback
 */
class AwardFeedback extends Model
{
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
