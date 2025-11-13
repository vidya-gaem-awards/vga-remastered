<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperPermission
 */
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
     * @TODO: this function is bad and doesn't do eager loading properly
     */
    public function getChildrenRecursive(): Collection
    {
        if (!str_starts_with($this->id, 'LEVEL')) {
            return collect();
        }

        /** @var Collection<array-key, Permission> $permissions */
        $permissions = collect();

        $childPermissions = $this->children()
            ->with('parents')
            ->with('children')
            ->get();

        foreach ($childPermissions as $child) {
            foreach ($child->getChildrenRecursive() as $grandchild) {
                $permissions->add($grandchild);
            }
            $permissions->add($child);
        }

        return $permissions;
    }
}
