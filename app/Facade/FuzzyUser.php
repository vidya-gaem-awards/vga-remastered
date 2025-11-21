<?php

namespace App\Facade;

use App\Services\FuzzyUserService;
use Illuminate\Support\Facades\Facade;

class FuzzyUser extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FuzzyUserService::class;
    }
}
