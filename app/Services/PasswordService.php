<?php

namespace App\Services;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    public function showChangePasswordPage()
    {
        return view('auth.change-password');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        // Check if the old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'The old password is incorrect.']);
        }
        // Check if new password is the same as the old one
        if ($request->old_password === $request->new_password) {
            return back()->with('error', 'New password cannot be the same as the old password.');
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return back()->with('success', 'Password updated successfully!');
    }
}