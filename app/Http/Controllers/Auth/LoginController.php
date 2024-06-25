<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view("auth.login")->with('success', 'login viewed successfully.');
    }
    public function store(LoginRequest $request)
    {
        //validate
        // login the user
        //regenerate session token
        // redirect
        $attributes = $request->validated();
        if (!Auth::attempt($attributes, $request->filled('remember'))) {
            throw ValidationException::withMessages(['email' => 'this may be wrong', 'password' => 'this may be wrong']);
        }
        $request->session()->regenerate();
        return redirect('/');

    }
    public function destroy()
    {
        Auth::logout();
        return redirect('/login');
    }
}