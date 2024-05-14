<?php

namespace App\Models\Times;

use Illuminate\Database\Eloquent\Model;

use App\Models\Allocations\Allocation;
use App\Models\Books\Book;
use App\Models\Trails\MasungiTrail;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class MasungiTimeSlot extends Model
{
    use SoftDeletes;
    protected $connection = 'masungi';
    protected $table = 'time_slots';

    public static function getAvailable($date, $trail_id)
    {
        $date = Carbon::parse($date);
        $weekday = $date->isWeekday() ? 'Weekdays' : 'Weekend & Holidays';

        $trail = Allocation::find($trail_id);

        $bookings = Book::whereDate('scheduled_at', $date->format('Y-m-d'))
            ->whereHas('invoice', function($invoice) {
                $invoice->where('is_firstpayment_paid', true);
                $invoice->whereNull('rejected_reason');
            })
            ->where('allocation_id', $trail_id)
            ->whereNull('deleted_at')
            ->pluck('start_time')
            ->toArray();

        // $masungi_trail_id = str_contains($trail->name, 'Discovery') ? 1 : 3; // !!! Hard coded!
        $masungi_trail = MasungiTrail::where('name', $trail->name)->first();

        $timeslots = MasungiTimeSlot::where('day_week_name', $weekday)->where('trail_id', $masungi_trail->id)->orderBy('order')->pluck('time')->toArray();

        $diff = array_diff($timeslots, $bookings);

        return array_map(function($item) { return ['id' => $item, 'name' => $item ]; }, $diff);
    }
}
