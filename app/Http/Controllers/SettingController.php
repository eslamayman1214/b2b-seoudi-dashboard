<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService, private LogHelper $logService)
    {
    }

    public function index()
    {
        $settings = $this->settingService->getSettings();
        $user = Auth::user();
        $this->logService->logAction('View Settings', "Settings page viewed by user ID: {$user->id}, Name: {$user->name}, Email: {$user->email}.");
        return view('settings.index', $settings);
    }

    public function saveSettings(Request $request)
    {
        $this->settingService->saveSettings($request);
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    public function toggleLogging(Request $request)
    {
        $newValue = $this->settingService->toggleLogging($request);
        $user = Auth::user();
        $action = $newValue == '1' ? 'Enable Logging' : 'Disable Logging';
        $this->logService->logAction($action, "Logging status changed to {$newValue} by user ID: {$user->id}, Name: {$user->name}, Email: {$user->email}.");
        return redirect()->route('settings.index')->with('success', 'Logging status updated successfully.');
    }
}
