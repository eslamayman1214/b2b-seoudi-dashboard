<?php

namespace App\Helpers;

use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogHelper
{
    public static function logAction($action, $details)
    {
        $loggingEnabled = Configuration::where('key', 'logging_enabled')->value('value');

        if ($loggingEnabled) {
            $time = now()->toDateTimeString();
            $user = Auth::user();

            if ($user) {
                $logEntry = "{$time} | User: {$user->name} | Role: {$user->role} | Action: {$action} | Details: {$details}\n";
            } else {
                $logEntry = "{$time} | User: Guest | Action: {$action} | Details: {$details}\n";
            }

            Log::channel('actionlog')->info($logEntry);

        }
    }
}
