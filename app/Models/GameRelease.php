<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperGameRelease
 */
class GameRelease extends Model
{
    use SoftDeletes;

    public const PLATFORMS = [
        'pc' => 'PC',
        'vr' => 'VR',
        'ps3' => 'PS3',
        'ps4' => 'PS4',
        'ps5' => 'PS5',
        'vita' => 'Vita',
        'psn' => 'PSN',
        'x360' => '360',
        'xb1' => 'XB1',
        'xsx' => 'XSX',
        'xbla' => 'XBLA',
        'wii' => 'Wii',
        'wiiu' => 'Wii U',
        'switch' => 'Switch',
        'switch2' => 'Switch 2',
        'wiiware' => 'WiiWare',
        'n3ds' => '3DS',
        'mobile' => 'Mobile'
    ];

    protected function casts(): array
    {
        return [
            'platforms' => 'array',
        ];
    }

    protected function pc(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => in_array('pc', $this->platforms, true),
        );
    }

    protected function xbox(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => in_array('x360', $this->platforms, true) || in_array('xb1', $this->platforms, true) || in_array('xsx', $this->platforms, true),
        );
    }

    protected function playstation(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => in_array('ps3', $this->platforms, true) || in_array('ps4', $this->platforms, true) || in_array('ps5', $this->platforms, true) || in_array('vita', $this->platforms, true),
        );
    }

    protected function nintendo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => in_array('switch', $this->platforms, true) || in_array('switch2', $this->platforms, true),
        );
    }

    protected function mobile(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => in_array('mobile', $this->platforms, true),
        );
    }

    protected function vr(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => in_array('vr', $this->platforms, true),
        );
    }
}
