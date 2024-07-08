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

    public function apiLoginUser(LoginRequest $request)
    {
        $validated = $request->validated();

        if (Auth::attempt($validated)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
        }

        // If the email exists but the password is incorrect
        if (User::where('email', $request->input('email'))->exists()) {
            return response()->json(['message' => 'The email or password is not correct'], 401);
        }

        // If the email does not exist or is invalid
        return response()->json(['message' => 'The email you entered is not valid'], 400);
    }
}
