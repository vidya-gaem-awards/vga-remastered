<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperAward
 */
class Award extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'subtitle',
        'comments',
        'order',
        'enabled',
        'nominations_enabled',
        'secret',
    ];

    protected function casts(): array
    {
        return [
            'enabled'             => 'boolean',
            'nominations_enabled' => 'boolean',
            'secret'              => 'boolean',
        ];
    }

    #[Scope]
    protected function notSecret(Builder $query): void
    {
        $query->where('secret', false);
    }

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

    public function getGroupedFeedback(): array
    {
        /** @var Collection<array-key, int> $feedback */
        $feedback = $this->awardFeedback->groupBy('opinion')->map->count();

        $positive = $feedback[1] ?? 0;
        $negative = $feedback[-1] ?? 0;

        return [
            'positive' => $positive,
            'negative' => $negative,
            'net' => $positive - $negative,
            'total' => $positive + $negative,
        ];
    }

    public function getFeedbackPercent(): array
    {
        $feedback = $this->getGroupedFeedback();

        if ($feedback['total'] === 0) {
            return [
                'positive' => 0,
                'negative' => 0
            ];
        }

        return [
            'positive' => $feedback['positive'] / $feedback['total'] * 100,
            'negative' => $feedback['negative'] / $feedback['total'] * 100
        ];
    }

    public function jsonSerialize(): array
    {
        return [
            'id'                 => $this->id,
            'slug'               => $this->slug,
            'name'               => $this->name,
            'subtitle'           => $this->subtitle,
            'comments'           => $this->comments,
            // @TODO: this is going to cause conflicts now that we're using integer IDs for
            //        both awards and autocompleters.
            'autocompleter'      => $this->autocompleter?->id ?? $this->id,
            'order'              => $this->order,
            'enabled'            => $this->enabled,
            'nominationsEnabled' => $this->nominations_enabled,
            'secret'             => $this->secret,
        ];
    }
}
