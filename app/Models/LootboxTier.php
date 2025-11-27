<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperLootboxTier
 */
class LootboxTier extends Model
{
    public function lootboxItems(): HasMany
    {
        return $this->hasMany(LootboxItem::class);
    }

    public static function getTotalRelativeDropChance(): float
    {
        return (float) self::sum('drop_chance');
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dropChance' => $this->drop_chance,
            'color' => $this->color,
        ];
    }
}
