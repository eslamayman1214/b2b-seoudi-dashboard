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

        $customerEndpoint = Configuration::where('key', 'customer_endpoint')->value('value');
        $customerToken = Configuration::where('key', 'customer_token')->value('value');
        $baseUrl = Configuration::where('key', 'base_url')->value('value');
        $productsEndpoint = Configuration::where('key', 'products_endpoint')->value('value');
        $productsToken = Configuration::where('key', 'products_token')->value('value');

        return compact('loggingEnabled', 'customerEndpoint', 'customerToken', 'baseUrl', 'productsEndpoint', 'productsToken');
    }

    public function saveSettings(Request $request)
    {
        $this->validateInput($request);

        $loggingConfig = Configuration::firstOrCreate(['key' => 'logging_enabled']);
        $loggingConfig->value = $request->input('logging') == '1' ? '1' : '0';
        $loggingConfig->save();

        $settings = [
            'customer_endpoint' => $request->input('customer_endpoint') ?? '',
            'customer_token' => $request->input('customer_token') ?? '',
            'base_url' => $request->input('base_url') ?? '',
            'products_endpoint' => $request->input('products_endpoint') ?? '',
            'products_token' => $request->input('products_token') ?? '',
        ];

        foreach ($settings as $key => $value) {
            Configuration::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
    private function validateInput(Request $request)
    {
        $request->validate([
            'base_url' => 'nullable|string',
            'customer_endpoint' => 'nullable|string',
            'customer_token' => 'nullable|string',
            'products_endpoint' => 'nullable|string',
            'products_token' => 'nullable|string',
        ]);

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
