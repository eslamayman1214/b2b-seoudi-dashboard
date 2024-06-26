<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\InviteRequest;
use App\Mail\InvitationEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function create()
    {
        LogHelper::logAction('View Invite Page', 'Invite page viewed.');
        return view('auth.invite');
    }

    public function send(InviteRequest $request)
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
        LogHelper::logAction('Invite User', "Invited user: {$user->email}");

        return redirect()->route('products.index')->with('status', 'User added successfully.');
    }
}