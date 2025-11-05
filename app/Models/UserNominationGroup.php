<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(UserNominationGroup::class, 'merged_into_id');
    }

    protected function casts(): array
    {
        return [
            'ignored' => 'boolean',
        ];
    }
}
