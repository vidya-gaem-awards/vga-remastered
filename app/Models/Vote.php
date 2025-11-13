<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperVote
 */
class Vote extends Model
{
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'preferences' => 'array',
        ];
    }
}
