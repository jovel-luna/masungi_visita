<?php

namespace App\Models\Books;

use Illuminate\Validation\ValidationException;
use App\Extenders\Models\BaseModel as Model;

use App\Models\Violations\GroupViolation;
use App\Models\Remarks\GroupRemark;
use App\Models\AddOns\AddOn;
use App\Models\Allocations\Allocation;
use App\Models\Destinations\Destination;
use App\Models\Guests\Guest;
use App\Models\Feedbacks\GuestFeedback;
use App\Models\Invoices\Invoice;

use App\Traits\FileTrait;
use App\Models\Users\Management;

use Carbon\Carbon;

class Book extends Model
{

    use FileTrait;

    protected $dates = ['scheduled_at', 'ended_at', 'started_at'];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $guests = $this->guests;
        $searchable = [
            'id' => $this->id,
            'destination' => $this->destination ? $this->destination->name : '',
            'allocation' => $this->allocation ? $this->allocation->name : '',
            // 'time' => $this->time,
            'total_guest' => $this->total_guest,
            'is_walkin' => $this->is_walkin == 1 ? 'Walk-In' : 'Online',
            'guest' => $guests,
            'status' => $this->getStatus(),
            'start_time' => str_replace(':','',Carbon::parse($this->start_time)->format('h:i A')),
            'point_person' => count($this->guests) ? $this->guests->where('main', 1)->first()->renderFullname() : null, 
        ];
        
        return $searchable;
    }

    /**
     * Morph relationship to Management and User Models
     */
    public function bookable()
    {
        return $this->morphTo();
    }

	public function addOns()
	{
		return $this->hasMany(AddOn::class);
	}

    public function groupViolations() 
    {
    	return $this->hasMany(GroupViolation::class);
    }

    public function groupRemarks() 
    {
    	return $this->hasMany(GroupRemark::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class)->withTrashed();
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class)->withTrashed();
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function guestFeedbacks() 
    {
        return $this->hasMany(GuestFeedback::class);
    }
    
    public function representative()
    {
        return $this->belongsTo(Management::class, 'destination_representative_id')->withTrashed();
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class)->withTrashed();
    }

    /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['allocation_id', 'scheduled_at', 'start_time'], $destination_id)
    {
        $vars = [];
        $vars['allocation_id'] = $request->allocation_id;
        $vars['scheduled_at'] = Carbon::parse($request->scheduled_at)->format('Y-m-d H:i:s');
	$vars['start_time'] = Carbon::parse($request->start_time)->format('H:i:s');
	$vars['add_on_id'] = $request->add_on_id;
        // $vars = $request->only($columns);
        // $vars['allocation_id'] = $request->allocation_id;
        // $vars['scheduled_at'] = $request->scheduled_at;
        // $vars['start_time'] = $request->scheduled_at;
        // $vars['re_scheduled_at'] = $request->scheduled_at;
        // $vars['destination_id'] = $destination_id;
        // $vars['total_guest'] = $request->total_guest;
        // $vars['is_walkin'] = true;
        
        if (!$item) {
            $item = static::create($vars);
            $item = auth()->user()->books()->create($vars);
        } else {
            $item->update($vars);
            // $item->total_guest = $request->total_guest;
            // $item->save();
        }

        return $item;
    }

    /**
     * @Render
     */
    public function renderShowUrl($prefix = 'admin') {
        return route($prefix . '.bookings.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.bookings.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.bookings.restore', $this->id);
    }

    public static function generateRandomString($length = 15, $additionalString = null)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($characters);

        $randomString = null;

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }

        $randomString .= $additionalString;
        
        return 'VST'.$randomString;
    }

    public function renderName($first_column = 'first_name', $second_column = 'last_name') {
        return $this->guests->first();
    }

    public function getStatus() {
        $started = $this->started_at;
        $ended = $this->ended_at;
        $status = 'Queue';
        if($started != null && $ended == null) {
            $status = 'Ongoing';
        } 

        if($started != null && $ended != null) {
            $status = 'Finished';
        }

        return $status;
    }

    public function getGuests() {
        $result = [];

        foreach ($this->guests as $guest) {
            array_push($result, [
                'name' => $guest->renderFullname(),
                'email' => $guest->email,
                'main' => $guest->main ? true : false,
                'nationality' => $guest->nationality,
                'birthdate' => $guest->birthdate,
                'contact_number' => $guest->contact_number,
            ]);
        }

        return $result;
    }

    public function renderTime() {
        return Carbon::parse($this->scheduled_at)->format('g:i A');
    }

    public function checkBookingAvailability($allocation_id, $scheduled_at, $start_time) {
        $exists = static::where('id', '!=', $this->id)
                        // Check if the booking has been approved
                        ->whereHas('invoice', function($invoice) {
                            $invoice->where('is_firstpayment_paid', true);
                            $invoice->whereNull('rejected_reason');
                        })
                        ->where('allocation_id', $allocation_id)
                        ->whereDate('scheduled_at', Carbon::parse($scheduled_at)->format('Y-m-d'))
                        ->where('start_time', Carbon::parse($start_time)->format('H:i:s'))
                        ->exists();
        if($exists) {
            throw ValidationException::withMessages([
                'Booking for this scheduled timeslot already exists'
            ]);
            return false;
        }
        return true;
    }
}
