<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperAutocompleter
 */
class Autocompleter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'strings',
    ];

    protected function casts(): array
    {
        return [
            'strings' => 'array',
        ];
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }
}
