<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic if needed
    }

    public function rules()
    {
        return [
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();

            if (!Hash::check($this->old_password, $user->password)) {
                $validator->errors()->add('old_password', 'The old password is incorrect.');
            }
        });
    }
}