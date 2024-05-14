<?php

namespace App\Models\Books;

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class AvailableSlots extends Model
{

    protected $dates = ['scheduled_at', 'ended_at', 'started_at'];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */

    protected $table = 'available_dates';
    
}
