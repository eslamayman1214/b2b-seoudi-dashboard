<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadTierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tiers_csv_file' => 'mimes:xlsx,xls,csv,txt|max:2048', // Tiers CSV file validation
        ];
    }
}
