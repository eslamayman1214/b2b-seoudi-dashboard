<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Http\Request;

class SettingService
{
    public function getSettings()
    {
        $loggingConfig = Configuration::where('key', 'logging_enabled')->first();
        $loggingEnabled = $loggingConfig ? $loggingConfig->value == '1' : false;

        return compact('loggingEnabled');
    }

    public function toggleLogging(Request $request)
    {
        $loggingConfig = Configuration::firstOrCreate(['key' => 'logging_enabled']);
        $newValue = $request->input('logging') == '1' ? '1' : '0';
        $loggingConfig->value = $newValue;
        $loggingConfig->save();

        return $newValue;
    }
}