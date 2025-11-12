<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TableHistory extends Model
{
    protected $table = 'table_history';

    const UPDATED_AT = null;

    public $fillable = ['table', 'entry', 'values'];

    public static function makeWith(string $modelClass, int|string $id, array $values): self
    {
        // No need to store the CSRF token
        unset($values['_token']);

        return new self([
            'table' => $modelClass,
            'entry' => $id,
            'values' => $values,
        ]);
    }

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
