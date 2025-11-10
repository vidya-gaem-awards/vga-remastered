<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TableHistory extends Model
{
    protected $table = 'table_history';

    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function action(): HasOne
    {
        return $this->hasOne(Action::class);
    }

    protected function casts(): array
    {
        return [
            'values' => 'array',
        ];
    }
}
