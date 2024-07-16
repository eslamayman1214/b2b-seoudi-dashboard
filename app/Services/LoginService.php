<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function showLoginPage()
    {
        return view("auth.login")->with('success', 'Login viewed successfully.');
    }

    public function validateUser(LoginRequest $request)
    {
        $attributes = $request->validated();
        if (Auth::validate($attributes)) {
            return Auth::getLastAttempted();
        }
        return null;
    }

    public function loginUser(LoginRequest $request)
    {
        $user = Auth::user();
        Auth::login($user);
        $request->session()->regenerate();
        return redirect('/');
    }
    public function findUserById($userId)
    {
        return User::find($userId);
    }

    public function logoutUser()
    {
        Auth::logout();
        return redirect('/login');
    }
}
