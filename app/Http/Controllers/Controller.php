<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected static array $flashes = [];

    /**
     * @TODO: This is a little bit of a hack. Should be implemented properly in the session instead
     *        of in the controller.
     */
    public function addFlash(string $type, string $message): void
    {
        static::$flashes[$type][] = $message;
        request()->session()->flash($type, static::$flashes[$type]);
    }
}
