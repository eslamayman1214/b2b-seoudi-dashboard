<?php

namespace App\Http\Requests;

use App\Rules\EmailDomain;
use Illuminate\Foundation\Http\FormRequest;

class InviteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic if needed
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => ['required', 'email', 'string', 'max:255', 'unique:users,email', new EmailDomain],
            'role' => 'required|in:admin,user',
        ];
    }
}