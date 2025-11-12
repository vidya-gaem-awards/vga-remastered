<?php

namespace App\Services;

use App\Models\Action;
use App\Models\TableHistory;
use Illuminate\Support\Facades\Auth;

/**
 * @TODO: This service should be completely replaced by a standard auditing package.
 *        Because of this, I haven't bothered to change anything about how the service works.
 *        The Action and TableHistory classes have been given static makeWith functions that have
 *        the same signature as their previous constructors.
 */
class AuditService
{
    /**
     * @TODO: This method doesn't currently handle anonymous users.
     */
    public function add(Action $action, ?TableHistory $history = null): void
    {
        $user = Auth::user();

        if ($history) {
            if ($user) {
                $history->user()->associate($user);
            }
            $history->save();
            $action->tableHistory()->associate($history);
        }

        if ($user) {
            $action->user()->associate($user);
        }
        $action->save();
    }
}
