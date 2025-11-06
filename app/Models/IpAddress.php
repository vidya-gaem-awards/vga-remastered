<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    protected function casts(): array
    {
        return [
            'whitelisted' => 'boolean',
        ];
    }
}
