<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        Redirect::setIntendedUrl($request->input('redirect'));

        return Socialite::driver('steam')->redirect();
    }

    public function loginReturn(): RedirectResponse
    {
        $socialiteUser = Socialite::driver('steam')->user();

        $user = User::where('steam_id', $socialiteUser->id)->first();

        if (!$user) {
            $user              = User::make();
            $user->first_login = now();
            $user->steam_id    = $socialiteUser->id;
        }

        $user->name       = $socialiteUser->nickname;
        $user->avatar_url = $socialiteUser->avatar;
        $user->last_login = now();
        $user->save();
        $user->logins()->create();

        Auth::login($user, remember: true);

        return redirect()->intended();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($request->input('redirect') ?: '/');
    }
}
