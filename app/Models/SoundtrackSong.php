<?php

namespace App\Models;

use App\Enums\SoundtrackSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperSoundtrackSong
 */
class SoundtrackSong extends Model
{
    protected $guarded = ['id'];

    /**
     * Returns all songs grouped by section
     *
     * @return Collection<string, \Illuminate\Database\Eloquent\Collection<int, SoundtrackSong>>
     */
    public static function allBySection(): Collection
    {
        return static::all()->groupBy('section')->mapWithKeys(function ($songs, $section) {
            return [$section => $songs->sortBy('order')->values()];
        })->filter();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'section' => SoundtrackSection::class,
        ];
    }
}
