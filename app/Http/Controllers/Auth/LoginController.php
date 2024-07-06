<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $loginService;
    protected $logService;

    public function __construct(LoginService $loginService, LogHelper $logService)
    {
        $this->loginService = $loginService;
        $this->logService = $logService;
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

    public function apiStore(LoginRequest $request)
    {
        return $this->loginService->apiLoginUser($request);
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