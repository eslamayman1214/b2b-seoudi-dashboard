<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRejectionReasonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Update authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customerId' => 'required|integer',
            'rejectedReasonId' => 'required',
            'note' => 'required_if:rejectedReasonId,238', // Require note if "Other" is selected
        ];
    }

    /**
     * Customize validation error messages.
     */
    public function messages(): array
    {
        return [
            'customerId.required' => 'Customer ID is required.',
            'rejectedReasonId.required' => 'Rejection reason is required.',
            'note.required_if' => 'A note is required when the rejection reason is "Other".',
        ];
    }
}
