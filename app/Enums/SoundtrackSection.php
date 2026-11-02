<?php

namespace App\Enums;

enum SoundtrackSection: string
{
    case Preshow = 'preshow';
    case Main = 'main';
    case Bonus = 'bonus';

    public function label(): string
    {
        return $this->name;
    }
}
