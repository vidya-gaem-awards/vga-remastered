<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use SoftDeletes;

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    protected function casts(): array
    {
        return [
            'nominations_enabled' => 'boolean',
            'secret'              => 'boolean',
        ];
    }
}
