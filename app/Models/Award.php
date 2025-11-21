<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

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

    protected static function booted(): void
    {
        static::addGlobalScope('enabled', function (Builder $builder) {
            $builder->where('enabled', true);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @TODO: I wonder if this should be a global scope? Could go either way
     */
    #[Scope]
    protected function hideSecret(Builder $query): Builder
    {
        // I don't know if this is considered a good idea?
        // I'm going to do it anyway, because it's very convenient.
        if (Gate::allows('awards_secret')) {
            return $query;
        }
        return $query->where('secret', false);
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
        return $this->hasMany(Nominee::class)->orderBy('name');
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

    public function getNameSuggestions(bool $sortAlphabetically = false): array
    {
        $suggestions = [];

        foreach ($this->awardSuggestions as $suggestion) {
            $normalised = strtolower(trim($suggestion->suggestion));
            if (!isset($suggestions[$normalised])) {
                $suggestions[$normalised] = [
                    'count' => 0,
                    'title' => $suggestion->suggestion,
                ];
            }
            $suggestions[$normalised]['count']++;
        }

        if ($sortAlphabetically) {
            usort($suggestions, function ($a, $b) {
                return strtolower($a['title']) <=> strtolower($b['title']);
            });
        } else {
            usort($suggestions, function ($a, $b) {
                if ($b['count'] === $a['count']) {
                    return strtolower($a['title']) <=> strtolower($b['title']);
                }
                return $b['count'] <=> $a['count'];
            });
        }

        return $suggestions;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'                 => $this->id,
            'slug'               => $this->slug,
            'name'               => $this->name,
            'subtitle'           => $this->subtitle,
            'comments'           => $this->comments,
            'autocompleter'      => $this->autocompleter
                                        ? 'auto_' . $this->autocompleter->id
                                        : 'award_' . $this->id,
            'order'              => $this->order,
            'enabled'            => $this->enabled,
            'nominationsEnabled' => $this->nominations_enabled,
            'secret'             => $this->secret,
        ];
    }
}
