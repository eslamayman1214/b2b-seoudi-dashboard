<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function showLoginPage()
    {
        return view("auth.login")->with('success', 'Login viewed successfully.');
    }

    public function loginUser(Request $request)
    {
        $attributes = $request->validated();
        if (!Auth::attempt($attributes, $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'This may be wrong',
                'password' => 'This may be wrong',
            ]);
        }
        $request->session()->regenerate();
        return redirect('/');
    }

    public function logoutUser()
    {
        $user = Auth::user();
        Auth::logout();
        return redirect('/login');
    }

    public function apiLoginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}