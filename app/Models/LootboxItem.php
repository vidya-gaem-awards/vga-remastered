<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperLootboxItem
 */
class LootboxItem extends Model
{
    protected function casts(): array
    {
        return [
            'css'    => 'boolean',
            'buddie' => 'boolean',
            'music'  => 'boolean',
        ];
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    public function musicFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'music_file_id');
    }

    public function lootboxTier(): BelongsTo
    {
        return $this->belongsTo(LootboxTier::class);
    }

    public function additionalFiles(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'lootbox_item_files');
    }

    public function userInventoryItems(): HasMany
    {
        return $this->hasMany(UserInventoryItem::class);
    }

    public static function getTotalRelativeDropChance(): float
    {
        return (float) self::sum('drop_chance');
    }

    public static function getTotalAbsoluteDropChance(): float
    {
        return (float) self::sum('absolute_drop_chance');
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'image' => $this->image,
            'css' => $this->css,
            'buddie' => $this->buddie,
            'music' => $this->music,
            'musicFile' => $this->musicFile,
            'cssContents' => $this->css_contents,
            'series' => $this->series,
            'tier' => $this->lootbox_tier_id,
            'dropChance' => $this->drop_chance,
            'absoluteDropChance' => $this->absolute_drop_chance,
            'extra' => $this->extra,
            'additionalFiles' => $this->additionalFiles,
        ];
    }
}
