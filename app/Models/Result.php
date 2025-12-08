<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperResult
 */
class Result extends Model
{
    public const string OFFICIAL_FILTER = '08-4chan-or-null-with-voting-code';
    public const string OFFICIAL_ALGORITHM = 'schulze';
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    #[Scope]
    protected function official(Builder $query): void
    {
        $query
            ->where('filter', self::OFFICIAL_FILTER)
            ->where('algorithm', self::OFFICIAL_ALGORITHM)
            ->where('time_key', 'latest');
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
