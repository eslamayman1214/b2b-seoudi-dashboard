<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\loginService;

class AuthController extends Controller
{
    public function __construct(private loginService $loginService)
    {
    }
    public function login(LoginRequest $request)
    {
        return $this->loginService->apiLoginUser($request);
    }
}
