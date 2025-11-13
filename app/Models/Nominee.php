<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperNominee
 */
class Nominee extends Model
{
    use SoftDeletes;

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    public function userNominationGroups(): HasMany
    {
        return $this->hasMany(UserNominationGroup::class);
    }
}
