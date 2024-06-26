<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogHelper
{
    public static function logAction($action, $details)
    {
        $user = Auth::user();
        $username = $user ? $user->name : 'Guest';
        $role = $user ? $user->role : 'N/A';
        $time = now()->toDateTimeString();

        $logMessage = "[{$time}] User: {$username}, Role: {$role}, Action: {$action}, Details: {$details}";

        Log::channel('actionlog')->info($logMessage);
    }
}