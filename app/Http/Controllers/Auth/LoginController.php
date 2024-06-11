<?php 

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class LoginController extends Controller
{
     public function create()
    {
        return view("auth.login");
    }
    public function store(Request $request)
    {
        //validate
        // login the user
        //regenerate session token
        // redirect
        $attributes = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => [
                'required',
                //'string',
                //'min:8',
                //'regex:/[a-z]/', // must include at least one lowercase letter
                //'regex:/[A-Z]/', // must include at least one uppercase letter
                //'regex:/[0-9]/', // must include at least one digit
                //'regex:/[@$!%*?&#]/', // must include at least one special character
                //  'confirmed',
                // password::default(),
            ],
        ]);
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