<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        LogHelper::logAction('View Login Page', 'Login page viewed.');
        return view("auth.login")->with('success', 'Login viewed successfully.');
    }
    public function store(LoginRequest $request)
    {
        $attributes = $request->validated();
        if (!Auth::attempt($attributes, $request->filled('remember'))) {
            LogHelper::logAction('Login Failed', "Failed login attempt for email: {$request->email}");
            throw ValidationException::withMessages(['email' => 'This may be wrong', 'password' => 'This may be wrong']);
        }
        $request->session()->regenerate();
        LogHelper::logAction('Login Successful', "User logged in: {$request->email}");
        return redirect('/');
    }
        public function destroy()
    {
        $user = Auth::user();
        LogHelper::logAction('Logout', "User logged out: {$user->email}");
        Auth::logout();
        return redirect('/login');
    }



     public function apiStore(LoginRequest $request)
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
   /* public function apiDestroy(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        LogHelper::logAction('Logout', "User logged out: {$user->email}");

        return response()->json(['message' => 'Logged out successfully.'], 200);
    }*/

}