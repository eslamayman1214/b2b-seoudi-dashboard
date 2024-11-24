<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRejectionReasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['optionLabel' => 'required|string|max:255'];
    }

    public function messages(): array
    {
        return [
            'optionLabel.required' => 'The rejection reason label is required.',
        ];
    }
}
