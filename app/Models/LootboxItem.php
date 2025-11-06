<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function casts(): array
    {
        return [
            'css'    => 'boolean',
            'buddie' => 'boolean',
            'music'  => 'boolean',
        ];
    }
}
