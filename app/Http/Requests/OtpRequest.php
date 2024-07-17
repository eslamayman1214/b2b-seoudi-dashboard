<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'otp' => 'required|digits:4',
        ];
    }
    public function messages()
    {
        return [
            'otp.digits' => 'The OTP must be exactly 4 digits.',
        ];
    }
}