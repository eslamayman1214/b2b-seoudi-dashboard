<?php

namespace App\Services;

use App\Http\Requests\InviteRequest;
use App\Mail\InvitationEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InviteService
{
    public function showInvitePage()
    {
        return view('auth.invite');
    }

    public function sendInvite(InviteRequest $request)
    {
        $validatedData = $request->validated();
        $password = Str::random(10);
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($password),
            'role' => $validatedData['role'],
        ]);

        Mail::to($user->email)->send(new InvitationEmail($user->name, $user->email, $password));
        return redirect()->route('products.index')->with('status', 'User added successfully.');
    }
}