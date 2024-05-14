<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Carbon\Carbon;
use App\Models\Books\Book;
use App\Models\BlockedDates\BlockedDate;
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
        
        $BlockedDate = BlockedDate::all();
        
        $block_dates = array();

        foreach($BlockedDate as $date){
            $block_dates[] = Carbon::parse($date->date);
        }

        $booking = Book::where('id', $this->book_id)->first();
   
        $start = Carbon::now();
        $end = Carbon::parse($booking->scheduled_at); 

        $days = $start->diffInDaysFiltered(function (Carbon $date) use ($block_dates) {
            return $date->isWeekday() && !in_array($date, $block_dates);
        }, $end);

        if ($days < 5  ) {
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
