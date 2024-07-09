<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\loginService;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(private loginService $loginService, private LogHelper $logService)
    {
    }

    public function create()
    {
        $this->logService->logAction('View Login Page', 'Login page viewed.');
        return $this->loginService->showLoginPage();
    }

    public function store(LoginRequest $request)
    {
        $this->logService->logAction('Login Attempt', "Login attempt for email: {$request->email}");
        return $this->loginService->loginUser($request);
    }

    public function destroy()
    {
        $user = Auth::user();
        $this->logService->logAction('Logout', "User logged out: {$user->email}");
        return $this->loginService->logoutUser();
    }
}
