<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $hidden = [
        'remember_token',
    ];

    protected $authPasswordName = null;

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
}
