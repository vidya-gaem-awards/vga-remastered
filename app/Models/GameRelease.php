<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameRelease extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'platforms' => 'array',
        ];
    }
}
