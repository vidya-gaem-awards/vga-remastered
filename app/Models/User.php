<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    protected $hidden = [
        'remember_token',
    ];

    protected $fillable = [
        'steam_id',
        'name',
        'avatar_url',
    ];

    protected $authPasswordName = null;
    private ?Collection $permissionCache = null;

    protected function casts(): array
    {
        return [
            'team_member' => 'boolean',
            'first_login' => 'datetime',
            'last_login'  => 'datetime',
        ];
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function votingCodeLogs(): HasMany
    {
        return $this->hasMany(VotingCodeLog::class);
    }

    public function logins(): HasMany
    {
        return $this->hasMany(Login::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function canDo(string $ability): bool
    {
        return $this->allPermissions()->pluck('id')->contains($ability);
    }

    public function allPermissions(): Collection
    {
        if ($this->permissionCache === null) {
            $this->populatePermissionCache();
        }
        return $this->permissionCache;
    }

    private function populatePermissionCache(): void
    {
        /** @var Collection<array-key, Permission> $permissions */
        $permissions = collect();

        $userPermissions = $this->permissions()
            ->with('children')
            ->with('parents')
            ->get();

        foreach ($userPermissions as $permission) {
            $permissions->add($permission);
            foreach ($permission->getChildrenRecursive() as $child) {
                if ( ! str_starts_with($child->id, 'LEVEL')) {
                    $permissions->add($child);
                }
            }
        }

        $this->permissionCache = $permissions;
    }
}
