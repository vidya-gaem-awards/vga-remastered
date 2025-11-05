<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Autocompleter extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'strings' => 'array',
        ];
    }
}
