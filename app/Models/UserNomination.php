<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperUserNomination
 */
class UserNomination extends Model
{
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function userNominationGroup(): BelongsTo
    {
        return $this->belongsTo(UserNominationGroup::class);
    }

    public function originalGroup(): BelongsTo
    {
        return $this->belongsTo(UserNominationGroup::class, 'original_group_id');
    }
}
