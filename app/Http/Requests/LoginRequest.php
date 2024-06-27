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
}