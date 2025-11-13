<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperIpAddress
 */
class IpAddress extends Model
{
    protected function casts(): array
    {
        return [
            'whitelisted' => 'boolean',
        ];
    }
}
