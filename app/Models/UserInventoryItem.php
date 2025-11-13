<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperUserInventoryItem
 */
class UserInventoryItem extends Model
{
    const UPDATED_AT = null;

    public function lootboxItem(): BelongsTo
    {
        return $this->belongsTo(LootboxItem::class);
    }
}
