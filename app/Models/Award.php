<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use SoftDeletes;

    public function winnerImage(): BelongsTo
    {
        return $this->belongsTo(File::class, 'winner_image_id');
    }

    public function autocompleter(): BelongsTo
    {
        return $this->belongsTo(Autocompleter::class);
    }

    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class);
    }

    public function userNominationGroups(): HasMany
    {
        return $this->hasMany(UserNominationGroup::class);
    }

    public function userNominations(): HasMany
    {
        return $this->hasMany(UserNomination::class);
    }

    public function awardSuggestions(): HasMany
    {
        return $this->hasMany(AwardSuggestion::class);
    }

    public function awardFeedback(): HasMany
    {
        return $this->hasMany(AwardFeedback::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    protected function casts(): array
    {
        return [
            'nominations_enabled' => 'boolean',
            'secret'              => 'boolean',
        ];
    }
}
