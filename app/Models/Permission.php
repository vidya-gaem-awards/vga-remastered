<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperPermission
 */
class Permission extends Model
{
    public const array STANDARD_PERMISSIONS = [
        'add_user' => 'Add a new level 1 user',
        'add_video_game' => 'Add a game to the autocomplete list',
        'adverts_manage' => 'View and manage the fake voting page ads',
        'arg_manage' => 'View and manage things relating to the ARG',
        'audit_log_view' => 'View the website\'s audit log',
        'autocompleter_edit' => 'Edit nomination autocompleters',
        'awards_delete' => 'Delete awards',
        'awards_edit' => 'Edit award information',
        'awards_feedback' => 'View award voting feedback',
        'awards_secret' => 'View secret awards',
        'bypass_mime_checks' => 'Bypass certain MIME checks when uploading files',
        'edit_config' => 'Edit site config, such as voting times',
        'LEVEL_1' => 'Provides limited access to non-secret data',
        'LEVEL_2' => 'Provides additional read-only access to slightly more information',
        'LEVEL_3' => 'Gives edit access to a number of things',
        'LEVEL_4' => 'Gives access to everything except for critical areas',
        'LEVEL_5' => 'Gives complete admin access',
        'items_manage' => 'View and manage the lootbox rewards',
        'items_manage_special' => 'Gives access to special lootbox functionality',
        'news_manage' => 'Add and delete news items',
        'news_view_user' => 'View the user that posted each news item',
        'nominations_edit' => 'Edit official nominees',
        'nominations_view' => 'View nominees and user nominations',
        'profile_edit_details' => 'Edit user details',
        'profile_edit_groups' => 'Edit user groups',
        'profile_edit_notes' => 'Edit notes attached to user profile',
        'profile_view' => 'View user profiles',
        'referrers_view' => 'View where site visitors are coming from',
        'tasks_nominees' => 'Complete nominee tasks on the tasks page',
        'tasks_view' => 'View the Remaining Tasks page',
        'view_debug_output' => 'Show detailed error messages when something goes wrong',
        'view_unfinished_pages' => 'View some pages before they are ready for the public',
        'voting_code' => 'View voting codes',
        'voting_results' => 'View voting results',
        'voting_view' => 'View the voting page',
    ];

    public const array STANDARD_PERMISSION_INHERITANCE = [
        'LEVEL_1' => ['add_video_game', 'awards_feedback', 'nominations_view', 'tasks_view', 'view_unfinished_pages', 'voting_view'],
        'LEVEL_2' => ['LEVEL_1', 'awards_secret', 'items_manage', 'news_view_user', 'profile_view', 'tasks_nominees', 'voting_code'],
        'LEVEL_3' => ['LEVEL_2', 'autocompleter_edit', 'awards_edit', 'nominations_edit', 'profile_edit_notes'],
        'LEVEL_4' => ['LEVEL_3', 'add_user', 'arg_manage', 'audit_log_view', 'news_manage', 'profile_edit_details', 'referrers_view', 'voting_results', 'adverts_manage', 'items_manage_special', 'bypass_mime_checks'],
        'LEVEL_5' => ['LEVEL_4', 'awards_delete', 'edit_config', 'profile_edit_groups']
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'permission_children', 'parent_id', 'child_id');
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'permission_children', 'child_id', 'parent_id');
    }

    /**
     * @TODO: this function is bad and doesn't do eager loading properly
     */
    public function getChildrenRecursive(): Collection
    {
        if (!str_starts_with($this->id, 'LEVEL')) {
            return collect();
        }

        /** @var Collection<array-key, Permission> $permissions */
        $permissions = collect();

        $childPermissions = $this->children()
            ->with('parents')
            ->with('children')
            ->get();

        foreach ($childPermissions as $child) {
            foreach ($child->getChildrenRecursive() as $grandchild) {
                $permissions->add($grandchild);
            }
            $permissions->add($child);
        }

        return $permissions;
    }
}
