<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit()
    {
        LogHelper::logAction('View Change Password Page', 'Change password page viewed.');
        return view('auth.change-password');
    }

    public function update(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            LogHelper::logAction('Change Password Failed', "Incorrect old password for user: {$user->email}");
            return back()->withErrors(['old_password' => 'The old password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        LogHelper::logAction('Change Password Successful', "Password changed for user: {$user->email}");
        return redirect()->route('products.index')->with('success', 'Password changed successfully.');
    }
}