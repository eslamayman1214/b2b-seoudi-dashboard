<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $loggingConfig = Configuration::where('key', 'logging_enabled')->first();
        $loggingEnabled = $loggingConfig ? $loggingConfig->value == '1' : false;

        // Log the action of viewing the settings page
        $user = Auth::user();
        $details = "Settings page viewed by user ID: {$user->id}, Name: {$user->name}, Email: {$user->email}.";
        LogHelper::logAction('View Settings', $details);

        return view('settings.index', compact('loggingEnabled'));
    }

    public function toggleLogging(Request $request)
    {
        $loggingConfig = Configuration::firstOrCreate(['key' => 'logging_enabled']);
        $newValue = $request->input('logging') == '1' ? '1' : '0';
        $loggingConfig->value = $newValue;
        $loggingConfig->save();

        // Log the action of enabling/disabling logging
        $user = Auth::user();
        $action = $newValue == '1' ? 'Enable Logging' : 'Disable Logging';
        $details = "Logging status changed to {$newValue} by user ID: {$user->id}, Name: {$user->name}, Email: {$user->email}.";
        LogHelper::logAction($action, $details);

        return redirect()->route('settings.index')->with('success', 'Logging status updated successfully.');
    }
}