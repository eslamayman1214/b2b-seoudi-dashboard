<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorize the user, adjust this logic if necessary
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|min:2|max:1000',
            'section' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'in:pending,in progress,resolved,re-open',
            'assigned' => 'nullable|exists:users,id',
        ];
    }
}
