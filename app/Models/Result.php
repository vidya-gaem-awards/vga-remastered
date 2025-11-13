<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperResult
 */
class Result extends Model
{
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    protected function casts(): array
    {
        return [
            'results'  => 'array',
            'steps'    => 'array',
            'warnings' => 'array',
        ];
    }
}
