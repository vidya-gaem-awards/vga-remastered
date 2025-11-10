<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LootboxItem extends Model
{
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

    protected function casts(): array
    {
        return [
            'css'    => 'boolean',
            'buddie' => 'boolean',
            'music'  => 'boolean',
        ];
    }
}
