<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(private loginService $loginService, private LogHelper $logService, private OtpService $otpService)
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
            $this->otpService->generateOtp($user);
            return view('auth.otp'); // Show OTP input view
        }

        return back()->withErrors(['email' => 'The email or password you entered is incorrect.']);
    }

    public function validateOtp(Request $request)
    {
        $userId = $request->session()->get('otp_user');
        $user = $this->loginService->findUserById($userId);

        if (!$user) {
            return response()->json(['errors' => ['email' => 'Session expired. Please login again.']], 422);
        }

        $otp = $request->input('otp');

        if (!preg_match('/^\d{4}$/', $otp)) {
            return response()->json(['errors' => ['otp' => 'The OTP must be exactly 4 digits.']], 422);
        }

        if ($this->otpService->validateOtp($user, $otp)) {
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

        $this->otpService->resendOtp($user);
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