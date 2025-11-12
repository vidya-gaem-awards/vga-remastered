<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    const UPDATED_AT = null;

    protected $guarded = ['id'];

    public static function makeWith(string $action, mixed $data1 = null, mixed $data2 = null): self
    {
        $backtrace = debug_backtrace();

        // @TODO: probably not right (although mostly matches Symfony behaviour).
        //        Needs to be fixed in a broader sense by adding full support for anonymous users.
        $ip = request()->header('CF-Connecting-IP', request()->ip());

        return self::make([
            'ip' => $ip,
            'page' => $backtrace[1]['class'] . '::' . $backtrace[1]['function'],
            'action' => $action,
            'data1' => $data1,
            'data2' => $data2,
        ]);
    }

    public function tableHistory(): BelongsTo
    {
        return $this->belongsTo(TableHistory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
