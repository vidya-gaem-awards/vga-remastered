<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'team_member' => 'boolean',
            'first_login' => 'timestamp',
            'last_login'  => 'timestamp',
        ];
    }
}
