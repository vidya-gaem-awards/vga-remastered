<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function userNominationGroup(): HasOne
    {
        return $this->hasOne(UserNominationGroup::class);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'flavorText' => $this->flavor_text,
            'image' => $this->image,
        ];
    }
}
