<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Permission;
use App\Models\TableHistory;
use App\Models\User;
use App\Services\AuditService;
use App\Services\SteamService;
use App\Settings\AppSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PeopleController extends Controller
{
    public function __construct(
        readonly private AuditService $auditService,
        readonly private SteamService $steam,
        readonly private AppSettings $settings,
    ) {
    }

    public function index(): View
    {
        $users = User::where('team_member', true)
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('people', [
            'users' => $users,
        ]);
    }

    public function permissions(): View
    {
        return view('people.permissions');
    }

    public function add(): View
    {
        $permissions = Permission::with('parents')
            ->orderBy('id')
            ->get();

        return view('people.add', [
            'permissions' => $permissions,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $steamId = $this->steam->stringToSteamId($request->input('id'));
        if (!$steamId) {
            return response()->json(['error' => 'Invalid SteamID or URL provided.']);
        }

        $profile = $this->steam->getProfile($steamId);
        if (!$profile) {
            return response()->json(['error' => 'no matches']);
        }

        /** @var User $user */
        $user = User::where('steam_id', $profile['steamId64'])->first();
        if (!$user) {
            $user = User::create([
                'steam_id' => $profile['steamId64'],
                'name' => $profile['nickname'],
                'avatar_url' => $profile['avatar'],
            ]);
        }

        if ($user->team_member) {
            return response()->json([
                'error' => 'already special',
                'name' => $user->name
            ]);
        }

        if ($request->boolean('add')) {
            if ($this->settings->read_only) {
                return response()->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
            }

            // Make the user special and give them the starting permission
            $user->team_member = true;

            if ($request->input('permission')) {
                $permission = Permission::find($request->input('permission'));
                if (!$permission) {
                    return response()->json(['error' => 'Invalid permission specified.']);
                }
                $user->permissions()->attach($permission);
            }
            $user->save();

            $this->auditService->add(
                Action::makeWith('user-added', $user->steam_id, $request->input('permission'))
            );

            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => true,
            'name' => $user->name,
            'avatar' => $user->avatar_url,
            'steamID' => $user->steam_id,
        ]);
    }

    public function view(User $user): View
    {
        if (!$user->team_member) {
            abort(404);
        }

        $permissions = Permission::with('parents')
            ->orderBy('id')
            ->get();

        return view('people.view', [
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }

    public function edit(User $user): View
    {
        if (!$user->team_member) {
            abort(404);
        }

        return view('people.edit', [
            'user' => $user,
        ]);
    }

    public function post(User $user, Request $request): RedirectResponse
    {
        if (!$user->team_member) {
            abort(404);
        }

        if ($this->settings->read_only) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return redirect()->back();
        }

        // Remove group
        if ($request->input('RemoveGroup') && Gate::allows('profile_edit_groups')) {
            $groupName = $request->input('RemoveGroup');

            /** @var Permission $group */
            $user->permissions()->detach($groupName);
            $this->addFlash('formSuccess', 'Permission ' . $groupName . ' successfully removed.');

            $this->auditService->add(
                Action::makeWith('profile-group-removed', $user->id, $groupName)
            );
        }

        // Add group
        if ($request->input('AddGroup') && Gate::allows('profile_edit_groups')) {
            $groupName = strtolower(trim($request->input('GroupName')));

            $group = Permission::find($groupName);
            if (!$group) {
                $this->addFlash('formError', 'Invalid group name.');
            } elseif ($user->permissions->contains($group)) {
                $this->addFlash('formError', 'User already has that permission.');
            } else {
                $user->permissions()->attach($group);
                $this->auditService->add(
                    Action::makeWith('profile-group-added', $user->id, $groupName)
                );

                $this->addFlash('formSuccess', 'Permission ' . $groupName . ' successfully added.');
            }
        }

        // Edit details (primary role and email)
        if ($request->input('action') === 'edit-details' && Gate::allows('profile_edit_details')) {
            $user->primary_role = $request->input('PrimaryRole');
            $user->email = $request->input('Email');
            $user->save();

            $this->auditService->add(
                Action::makeWith('profile-details-updated', $user->id),
                TableHistory::makeWith(User::class, $user->id, $request->all())
            );

            $this->addFlash('formSuccess', 'Details successfully updated.');
        }

        return redirect()->route('people.view', $user);
    }
}
