<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailDomain implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the email domain is seoudisupermarket.com
        return substr(strrchr($value, "@"), 1) === 'seoudisupermarket.com';
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Enter a valid email';
    }
}