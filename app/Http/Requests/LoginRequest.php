<?php

namespace App\Http\Requests;

use App\Rules\EmailDomain;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic if needed
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', new EmailDomain],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 'The email you entered is not valid.',
            'email.required' => 'The email field is required.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}
