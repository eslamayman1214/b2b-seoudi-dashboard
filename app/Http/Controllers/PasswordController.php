<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    protected $passwordService;
    protected $logService;

    public function __construct(PasswordService $passwordService, LogHelper $logService)
    {
        $this->passwordService = $passwordService;
        $this->logService = $logService;
    }

    public function edit()
    {
        $this->logService->logAction('View Change Password Page', 'Change password page viewed.');
        return $this->passwordService->showChangePasswordPage();
    }

    public function update(ChangePasswordRequest $request)
    {
        $this->logService->logAction('Change Password Attempt', "Change password attempt for user: " . Auth::user()->email);
        return $this->passwordService->changePassword($request);
    }
}