<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class more_than_5_banking_days implements Rule
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
        $difference = now()->diffInDays($value);
        if ($difference > 5 && $difference < 10 ) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Not Less than 10 banking days';
    }
}
