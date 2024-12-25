<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\CreditCategory;
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
        $categories = CreditCategory::all(); // Fetch all credit categories
        $user = Auth::user();
        $this->logService->logAction('View Settings', "Settings page viewed by user ID: {$user->id}, Name: {$user->name}, Email: {$user->email}.");
        return view('settings.index', array_merge($settings, ['categories' => $categories]));
    }

    public function saveSettings(Request $request)
    {
        $this->settingService->saveSettings($request);
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
    public function saveSlaLimit(Request $request, SettingService $settingService)
    {
        $settingService->saveSlaLimit($request);

        return redirect()->back()->with('success', 'SLA limit saved successfully!');
    }

    public function toggleLogging(Request $request)
    {
        $newValue = $this->settingService->toggleLogging($request);
        $user = Auth::user();
        $action = $newValue == '1' ? 'Enable Logging' : 'Disable Logging';
        $this->logService->logAction($action, "Logging status changed to {$newValue} by user ID: {$user->id}, Name: {$user->name}, Email: {$user->email}.");
        return redirect()->route('settings.index')->with('success', 'Logging status updated successfully.');
    }
    public function updateCategories(Request $request)
    {
        $validated = $request->validate([
            'categories.*.id' => 'required|exists:credit_categories,id',
            'categories.*.value' => 'required|numeric|min:0',
        ]);

        foreach ($validated['categories'] as $category) {
            $creditCategory = CreditCategory::find($category['id']);
            $creditCategory->value = $category['value'];
            $creditCategory->save();
        }

        $user = Auth::user();
        $this->logService->logAction('Update Credit Categories', "Credit categories updated by user ID: {$user->id}, Name: {$user->name}, Email: {$user->email}.");
        return redirect()->route('settings.index')->with('success', 'Credit categories updated successfully.');
    }
}
