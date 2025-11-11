<?php

if (!function_exists('year')) {
    function year(): string
    {
        return config('app.year');
    }
}
