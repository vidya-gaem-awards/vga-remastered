<?php

namespace App\Services;

use App\Models\Action;
use App\Models\Autocompleter;
use App\Models\Award;
use App\Models\Nominee;
use App\Models\TableHistory;
use App\Models\User;
use App\Models\UserNominationGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @TODO: This service should be completely replaced by a standard auditing package.
 *        Because of this, I haven't bothered to change anything about how the service works.
 *        The Action and TableHistory classes have been given static makeWith functions that have
 *        the same signature as their previous constructors.
 */
class AuditService
{
    public const array ACTIONS = [
        'profile-group-added' => 'Added a permission to a user',
        'profile-group-removed' => 'Removed a permission from a user',
        'profile-notes-updated' => 'Updated user notes',
        'profile-details-updated' => 'Updated user details',
        'user-added' => 'Added a user to the team',
        'award-added' => 'Created an award',
        'award-delete' => 'Deleted an award',
        'award-edited' => 'Edited an award',
        'add-video-game' => 'Added a video game to the main autocompleter',
        'remove-video-game' => 'Removed a video game from the main autocompleter',
        'reload-video-games' => 'Reloaded the list of video game releases',
        'config-updated' => 'Updated the website config',
        'mass-nomination-open' => 'Opened nominations for all awards',
        'mass-nomination-close' => 'Closed nominations for all awards',
        'nominee-new' => 'Added a nominee to an award',
        'nominee-delete' => 'Removed a nominee from an award',
        'nominee-edit' => 'Edited an award nominee',
        'winner-image-upload' => 'Uploaded an image for an award winner',
        'advert-new' => 'Created an advert',
        'advert-edit' => 'Edited an advert',
        'advert-delete' => 'Deleted an advert',
        'item-new' => 'Created a lootbox reward',
        'item-edit' => 'Edited a lootbox reward',
        'item-delete' => 'Deleted a lootbox reward',
        'cron-results-enabled' => 'Enabled the result generator process',
        'cron-results-disabled' => 'Disabled the result generator process',
        'config-readonly-enabled' => 'Turned on read-only mode',
        'template-added' => 'Added a new site template',
        'template-edited' => 'Edited a site template',
        'autocompleter-added' => 'Created an autocompleter',
        'autocompleter-edited' => 'Edited an autocompleter',
        'autocompleter-deleted' => 'Deleted an autocompleter',
        'nomination-group-ignored' => 'Ignored a user nomination for an award',
        'nomination-group-unignored' => 'Unignored a user nomination for an award',
        'nomination-group-merged' => 'Merged two user nominations',
        'nomination-group-demerged' => 'Demerged two user nominations',
        'nomination-group-updated' => 'Updated a user nomination group',
        'config-cache-cleared' => 'Cleared the website cache',
    ];

    /**
     * @TODO: This method doesn't currently handle anonymous users. (But did it ever?)
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

    public function getEntity(Action $action): ?object
    {
        if ($history = $action->tableHistory) {
            $class = $history->table;
            // The namespace AppBundle was renamed to App in the 2018 release
            $class = str_replace('AppBundle', 'App', $class);
            $class = str_replace('App\\Entity\\', 'App\\Models\\', $class);
            $id = $history->entry;

            if (!class_exists($class)) {
                return null;
            }

            if (!is_a($class, Model::class, true)) {
                return null;
            }

            $result = $class::find($id);
            if ($result) {
                return $result;
            }

            if (in_array($class, [Award::class, Nominee::class, Autocompleter::class], true)) {
                return $class::where('slug', $id)->first();
            }

            return null;
        } elseif (str_starts_with($action->action, 'profile') || $action->action === 'user-added') {
            return User::find($action->data1);
        } else {
            return null;
        }
    }

    public function getMultiEntity(Action $action): array
    {
        $default = $this->getEntity($action);

        $return = [
            'default' => $default,
        ];

        $entityClasses = match ($action->action) {
            'nomination-group-ignored',
            'nomination-group-unignored' => [Award::class, UserNominationGroup::class],
            'nomination-group-merged',
            'nomination-group-demerged' => [UserNominationGroup::class, UserNominationGroup::class],
            default => [],
        };

        if (isset($entityClasses[0]) && $action->data1) {
            $return['data1'] = $entityClasses[0]::find($action->data1);
        }
        if (isset($entityClasses[1]) && $action->data2) {
            $return['data2'] = $entityClasses[1]::find($action->data2);
        }

        return $return;
    }
}
