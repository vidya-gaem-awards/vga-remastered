<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperNews
 */
class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'text',
        'show_at',
    ];

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
            'show_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function new(): Attribute
    {
        return new Attribute(
            get: fn () => $this->show_at->gt(now()->subDays(2))
        );
    }
}
