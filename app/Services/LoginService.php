<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function __construct(private OtpService $otpService)
    {
    }

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

    public function findUserById($userId)
    {
        return User::find($userId);
    }

    public function logoutUser()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function validateOtp(User $user, string $otp): bool
    {
        return $this->otpService->validateOtp($user, $otp);
    }

    public function resendOtp(User $user)
    {
        return $this->otpService->resendOtp($user);
    }
}
