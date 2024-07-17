<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpRequest;
use App\Services\LoginService;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(private LoginService $loginService, private LogHelper $logService)
    {
    }

    public function create()
    {
        return $this->loginService->showLoginPage();
    }

    public function store(LoginRequest $request)
    {
        $user = $this->loginService->validateUser($request);

        if ($user) {
            // Temporarily store user ID in session to be used for OTP validation
            session(['otp_user' => $user->id]);
            $this->loginService->resendOtp($user);
            return view('auth.otp'); // Show OTP input view
        }

        return back()->withErrors(['email' => 'The email or password you entered is incorrect.']);
    }

    public function validateOtp(OtpRequest $request)
    {
        $userId = $request->session()->get('otp_user');
        $user = $this->loginService->findUserById($userId);

        if (!$user) {
            return response()->json(['errors' => ['email' => 'Session expired. Please login again.']], 422);
        }

        $otp = $request->input('otp');

        if ($this->loginService->validateOtp($user, $otp)) {
            Auth::login($user);
            $request->session()->regenerate();
            return response()->json(['status' => 'OTP validated successfully.', 'redirect' => '/']);
        }

        return response()->json(['errors' => ['otp' => 'The OTP you entered is incorrect or has expired. Please check your email for the correct OTP.']], 422);
    }

    public function resendOtp()
    {
        $userId = session('otp_user');
        $user = $this->loginService->findUserById($userId);

        if (!$user) {
            return response()->json(['errors' => ['email' => 'Session expired. Please login again.']], 422);
        }

        $this->loginService->resendOtp($user);
        return response()->json(['status' => 'A new OTP has been sent to your email.']);
    }

    public function destroy()
    {
        $user = Auth::user();
        if ($user) {
            return $this->loginService->logoutUser($user);
        }
        return redirect('/');
    }
}
