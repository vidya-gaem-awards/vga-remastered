<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LootboxTier extends Model
{
    public function lootboxItems(): HasMany
    {
        return $this->hasMany(LootboxItem::class);
    }
}
