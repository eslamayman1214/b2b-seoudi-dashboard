<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationEmail;

class InviteController extends Controller
{
    public function create()
    {
        return view('auth.invite');
    }

    public function send(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users|string|max:255',
            'role' => 'required|in:admin,user',
        ]);

        // Create the user account
        //$password = Str::random(10); // Generate a random password
        //$hashedPassword = Hash::make($password); // Hash the password
        $password = Str::random(10);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($password)
,
            'role' => $validatedData['role'],
        ]);

        // Redirect the user after sending the invitation
        Mail::to($user->email)->send(new InvitationEmail($user->name,$user->email,$password));
        return redirect()->route('products.index')->with('status', 'User added successfully.');
    }
}