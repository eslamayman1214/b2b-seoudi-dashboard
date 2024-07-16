<?php

namespace App\Services;

use App\Mail\OtpEmail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generateOtp($user)
    {
        $otp = rand(1000, 9999);
        Cache::put('otp_' . $user->email, $otp, now()->addMinutes(2));
        Mail::to($user->email)->send(new OtpEmail($user->name, $otp));
        return $otp;
    }

    public function validateOtp($user, $otp)
    {
        $cachedOtp = Cache::get('otp_' . $user->email);
        return $cachedOtp && $cachedOtp == $otp;
    }

    public function resendOtp($user)
    {
        return $this->generateOtp($user);
    }
}
