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

    /**
     * Checks if the current permission or any of its children provides the given ability.
     */
    public function hasAbility(string $ability): bool
    {
        if ($this->id === $ability) {
            return true;
        }

        // To avoid unnecessary database calls, we assume a permission can only have children if it's a LEVEL permission.
        if (!str_starts_with($this->id, 'LEVEL')) {
            return false;
        }

        return $this->children()
            ->with('children')
            ->get()
            ->contains(fn (Permission $child) => $child->hasAbility($ability));
    }
}
