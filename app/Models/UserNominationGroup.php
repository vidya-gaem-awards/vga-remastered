<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserNominationGroup extends Model
{
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Nominee::class);
    }

    public function mergedInto(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into_id');
    }

    public function mergedFrom(): HasMany
    {
        return $this->hasMany(self::class, 'merged_into_id');
    }

    public function userNominations(): HasMany
    {
        return $this->hasMany(UserNomination::class);
    }

    protected function casts(): array
    {
        return [
            'ignored' => 'boolean',
        ];
    }
}
