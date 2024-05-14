<?php

namespace App\Http\Controllers\Admin\Books;

use App\Extenders\Controllers\FetchController;

use App\Models\Books\Book;
use App\Models\Users\Management;
use Webpatser\Countries\Countries;
use App\Models\Fees\Fee;
use App\Models\Allocations\Allocation;
use App\Models\Types\VisitorType;
use App\Models\BlockedDates\BlockedDate;
use App\Models\Genders\Gender;
use App\Models\Guests\Guest;
use App\Models\ConservationFees\ConservationFee;

use DB;

use Carbon\Carbon;

class BookingFetchController extends FetchController
{
    /**
     * Set object class of fetched data
     * 
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new Book;
    }

    /**
     * Custom filtering of query
     * 
     * @param Illuminate\Support\Facades\DB $query
     * @return Illuminate\Support\Facades\DB $query
     */
    public function filterQuery($query)
    {
    	if($this->request->destination) {
            if($this->request->destination != 0) {
    	    	$query = $query->where('destination_id', $this->request->destination);
            }
    	}

    	if($this->request->experience) {
            if($this->request->experience != 0) {
                $query = $query->where('allocation_id', $this->request->experience);
            }
	    	
    	}

        if($this->request->payment_type) {
            if($this->request->payment_type == 1) {
                $query = $query->whereHas('invoice', function($query)  {
                    $query->where('bookable_type', 'App\Models\API\Masungi')->where('is_paypal_payment', true);

                });
            } elseif ($this->request->payment_type == 2) {
                $query = $query->whereHas('invoice', function($query)  {
                    /* For paynamics option excluding paypal*/
                    // $query->whereNotIn('bookable_type', ['App\Models\API\Masungi'])->where('is_paypal_payment', true);
                    /* For paynamics option including paypal*/
                    $query->whereNotIn('bookable_type', ['App\Models\API\Masungi']);
                });
            } elseif ($this->request->payment_type == 3) {
                $query = $query->whereHas('invoice', function($query)  {
                    $query->where('bookable_type', 'App\Models\API\Masungi')->where('is_paypal_payment', false);
                });
            }
        }

        if($this->request->payment_status) {
            if($this->request->payment_status == 1) {
                $query = $query->whereHas('invoice', function($query)  {
                    $query->where('is_paid', true);
                });
            } elseif ($this->request->payment_status == 2) {
                $query = $query->whereHas('invoice', function($query)  {
                    $query->where('is_paid', false)->where('is_fullpayment', false)->where('is_firstpayment_paid', true);
                });
            } elseif ($this->request->payment_status == 3) {
                $query = $query->whereHas('invoice', function($query)  {
                    $query->where('is_approved', false)->whereNull('rejected_reason');
                });
            } elseif ($this->request->payment_status == 4) {
                $query = $query->whereHas('invoice', function($query)  {
                    $query->whereNotNull('deleted_at');
                });
            }
        }

        if($this->request->visitor_type) {
            if($this->request->visitor_type != 0) {
                $type = $this->request->visitor_type;
                $query = $query->whereHas('guests', function($query) use($type) {
                    $query->where('main', true)->where('visitor_type_id', $type);
                });
            }
        }

        if($this->request->conservation_fee) {
            if($this->request->conservation_fee != 0) {
                $conservation_fee = $this->request->conservation_fee;
                $query = $query->whereHas('guests', function($query) use($conservation_fee) {
                    $query->where('main', true)->where('conservation_fee_id', $conservation_fee);
                });
            }
        }

	// already defined in Extender
#        if($this->request->filled('archived')) {
#	    $query = $query->whereHas('invoice', function($query)  {
#		    $query->whereNotNull('deleted_at')->where('is_paid', false)->where('is_approved', false);
#	     });	
#        }

        $admin = auth()->guard('admin')->user();
        
        if($admin->destination_id) {
            $query = $query->where('destination_id', $admin->destination_id);
        }

        return $query;
    }

    protected function searchQuery($query)
    {
        if ($this->request->filled('search')) {
            if (config('web.tnt.refresh_on_query')) {
                $this->class::get()->searchable();
            }

            $searchQuery = $this->class::search($this->request->input('search'));

            if ($this->request->filled('archived')) {
                $searchQuery = $searchQuery->withTrashed();
            }

            $query = $query->whereIn('id', $searchQuery->get()->pluck('id')->toArray());

        }
        return $query;
    }


    /**
     * Custom formatting of data
     * 
     * @param Illuminate\Support\Collection $items
     * @return array $result
     */
    public function formatData($items)
    {
        $result = [];

        foreach($items as $item) {
            // if($this->request->search) {
            //     $item = $item->guests->where('main', true)->where('emaill', 'like', '%'.$this->request->search.'%')->first()->book;
            // }

            $data = $this->formatItem($item);
            array_push($result, $data);
        }
        if($this->request->orderBy === 'point_person') {
            if($this->request->order == 'asc') {
                $result = collect($result)->sortBy('main_contact.fullname')->values()->all();
            } else {
                $result = collect($result)->sortByDesc('main_contact.fullname')->values()->all();
            }
        }

        if($this->request->orderBy === 'type') {
            if($this->request->order == 'asc') {
                $result = collect($result)->sortBy('main_contact.type')->values()->all();
            } else {
                $result = collect($result)->sortByDesc('main_contact.type')->values()->all();
            }
        }

        return $result;
    }



    /**
     * Build array data
     * 
     * @param  App\Contracts\AvailablePosition
     * @return array
     */
    protected function formatItem($item)
    {
        return [
            'id' => $item->id,
            'main_contact' => $this->getGuest($item->guests()->where('main', true)->get()),
            'agency_code' => $item->agency_code  == 'null' ? '---' : $item->agency_code,
            'is_walkin' => $item->is_walkin === 1 ? 'Walk-In' : 'Online',
            'from_masungi_reservation' => $item->from_masungi_reservation,
            'total_guest' => $item->total_guest,
            'allocation' => $item->allocation->name,
            'destination' => $item->destination->name,
            'scheduled_at' => $item->scheduled_at,
            'destination_id' => $item->destination->id,
            'allocation_id' => $item->allocation->id,
            'grand_total' => $item->invoice->grand_total,
            'transaction_fee' => $item->invoice->transaction_fee,
            'initial_payment' => '₱ '.$item->invoice->amount_settled,
            'balance' => '₱ '.$item->invoice->balance,
            'is_fullpayment' => $item->invoice->is_fullpayment ? 'Full Payment' : 'Half Payment' ,
            'payment_status' => $item->invoice->renderPaymentStatus(),
            'invoice_status' => $item->invoice->invoice_status,
            'time' => $item->start_time ? Carbon::createFromFormat('H:i:s', $item->start_time)->format('h:i A') : 'No visit time selected.',
            'status' => $item->ended_at != null ? 'Visit End ( '.$item->renderDate('ended_at').' )' : ($item->started_at == null ? 'Queue' : 'Started ( '.$item->renderDate('started_at').' )'),
            'qr_path' => $item->renderImagePath('qr_code_path'),
            'qr_id' => $item->qr_id,
            'created_at' => $item->renderDate(),
            'scheduled_at' => $item->renderDate('scheduled_at', 'F d, Y'),
            'showUrl' => $item->renderShowUrl(),
            'archiveUrl' => $item->renderArchiveUrl(),
            'restoreUrl' => $item->renderRestoreUrl(),
            'deleted_at' => $item->invoice->deleted_at,
        ];
    }

    public function getGuest($guests)
    {
    	$result = [];

        foreach($guests as $guest) {
            // if($guest->main === 1) {
            	$result['fullname'] = $guest->first_name.' '. $guest->last_name;
            	$result['email'] = $guest->email;
            	$result['contact_number'] = $guest->contact_number;
            	$result['type'] = $guest->conservationFee ? $guest->conservationFee->name : '---';
                $result['deleted_at'] = $guest->deleted_at;
            // }
        }

        return $result;
    }

    public function fetchView($id) {
        $item = null;

        if ($id) {
        	$item = Book::withTrashed()->findOrFail($id);
            $item->total_guests = $this->getGuests($item->guests()->where('main', false)->get());
            $main = $item->guests()->where('main', true)->first();
            $item->main_id = $main->id;
            $item->first_name = $main->first_name;
            $item->last_name = $main->last_name;
            $item->birthdate = $main->birthdate;
            $item->main_birthdate = Carbon::parse($main->birthdate)->format('Y-m-d');
            $item->gender = $main->gender;
            $item->nationality = $main->nationality;
            $item->contact_number = $main->contact_number;
            $item->email = $main->email;
            $item->visitor_type_id = $main->visitor_type_id;
            $item->special_fee_id = $main->special_fee_id;
            $item->conservation_fee_id = $main->conservation_fee_id;
            $item->specialFeeImagePath = $main->renderImagePath('special_fee_path');
            $item->emergency_contact_number = $main->emergency_contact_number;
            $item->archiveUrl = $item->renderArchiveUrl();
            $item->restoreUrl = $item->renderRestoreUrl();
        }
        $destination = Destination::all();
        $experiences = Allocation::with('fees')->get();
        $nationalities = Countries::all();
        $visitor_types = VisitorType::all();
        $blocked_dates = $this->blockedDates($destination);
        $genders = Gender::all();
        $fees = ConservationFee::getFormattedFees();
        $timeslots = MasungiTimeSlot::getAvailable($item->schedule, $item->allocation_id);
        $timeslots[] = ['id' => $item->start_time, 'name' => $item->start_time];
        
    	return response()->json([
    		'item' => $item,
            'timeslots' => $timeslots,
            'managements' => Management::all(),
            'experiences' => $experiences,
            'nationalities' => $nationalities,
            'visitor_types' => $visitor_types,
            'blocked_dates' => $blocked_dates,
            'genders' => $genders,
            'fees' => $fees,
    	]);
    }

    public function blockedDates($destination) {
        $destination = Destination::find($destination);
        $items = $destination->blockedDates;

        $result = [];

        foreach($items as $item) {
            foreach ($item->dates as $date) {
                array_push($result, [
                    Carbon::parse($date->date)->toDateString()
                ]);
            }
        }

        return $result;
    }

    public function getGuests($items) {
        $data = [];

        foreach ($items as $item) {
            array_push($data, [
                'id' => $item->id,
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'birthdate' => Carbon::parse($item->birthdate)->format('Y-m-d'),
                'gender' => $item->gender,
                'nationality' => $item->nationality,
                'contact_number' => $item->contact_number,
                'emergency_contact_number' => $item->emergency_contact_number,
                'email' => $item->email,
                'visitor_type_id' => $item->visitor_type_id,
                'special_fee_id' => $item->special_fee_id,
                'specialFeeImagePath' => $item->renderImagePath('special_fee_path'),
            ]);
        }

        return $data;
    }
}
