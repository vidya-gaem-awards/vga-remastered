<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'nominations_enabled' => 'boolean',
            'secret'              => 'boolean',
        ];
    }
}
