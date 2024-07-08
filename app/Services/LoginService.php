<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function showLoginPage()
    {
        return view("auth.login")->with('success', 'Login viewed successfully.');
    }

    public function loginUser(LoginRequest $request)
    {
        $attributes = $request->validated();
        if (!Auth::attempt($attributes, $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'The email or password you entered is incorrect.',
                'password' => 'The email or password you entered is incorrect.',
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

    public function apiLoginUser(LoginRequest $request)
    {
        $validated = $request->validated();
        if (Auth::attempt($validated)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
        }
        return response()->json(['message' => 'The email or password you entered is incorrect.'], 400);
    }
}
