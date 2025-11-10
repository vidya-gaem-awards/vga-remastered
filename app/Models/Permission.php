<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'permission_children', 'parent_id', 'child_id');
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'permission_children', 'child_id', 'parent_id');
    }
}
