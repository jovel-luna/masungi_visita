<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Carbon\Carbon;
use App\Models\Books\Book;
use Illuminate\Support\Facades\Log;

class less_than_5_banking_days implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($book_id)
    {
        $this->book_id = $book_id;
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

        $excludeWeekends = function ($date) {
            return $date->isWeekday(); // This will exclude weekends (Saturday and Sunday)
        };
        

        $difference = now()->diffInDaysFiltered($value, $excludeWeekends);
        Log::info('TEST IF TRAIL WORKiNG 2');
        if ($difference < 5  ) {
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
        return 'Not Less than 5 banking days';
    }
}
