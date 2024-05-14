<?php

namespace App\Http\Controllers\Admin\Books;

use App\Extenders\Controllers\FetchController;

use Illuminate\Http\Request;

use App\Models\Books\Book;
use App\Models\Users\Management;
use App\Models\Destinations\Destination;
use Webpatser\Countries\Countries;
use App\Models\Fees\Fee;
use App\Models\Allocations\Allocation;
use App\Models\Types\VisitorType;
use App\Models\BlockedDates\BlockedDate;
use App\Models\Genders\Gender;
use App\Models\ConservationFees\ConservationFee;
use App\Models\AddOns\MasungiAddOn;

use Carbon\Carbon;

class BookFetchController extends FetchController
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
        $date = request()->segments()[3]; 
        $destination = request()->segments()[4];
        $experience = request()->segments()[5];
    	$parsedDate = Carbon::parse($date)->toDateTimeString();
        return $query->whereDate('scheduled_at', $parsedDate)->where(['destination_id' => $destination, 'allocation_id' => $experience]);
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
            $data = $this->formatItem($item);
            array_push($result, $data);
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
            'main_contact' => $this->getGuest($item->guests),
            'is_walkin' => $item->is_walkin === 1 ? 'Walk-In' : 'Online',
            'total_guest' => $item->total_guest,
            'agency_code' => $item->agency_code  == 'null' ? '---' : $item->agency_code,
            'allocation' => $item->allocation->name,
            'time' => $item->start_time ? Carbon::createFromFormat('H:i:s', $item->start_time)->format('h:i A') : 'No visit time selected.',
            'status' => $item->ended_at != null ? 'Visit End ( '.$item->renderDate('ended_at').' )' : ($item->started_at == null ? 'Not yet started' : 'Started ( '.$item->renderDate('started_at').' )'),
            'qr_path' => $item->renderImagePath('qr_code_path'),
            'qr_id' => $item->qr_id,
            'created_at' => $item->renderDate(),
            'showUrl' => $item->renderShowUrl(),
            'archiveUrl' => $item->renderArchiveUrl(),
            'restoreUrl' => $item->renderRestoreUrl(),
            'deleted_at' => $item->deleted_at,
        ];
    }

    // protected function sortQuery($query) {
    //     dd()
    //     switch ($this->orderBy) {
    //         default:
    //                 $query = $query->orderBy($this->orderBy, 'desc');
    //             break;
    //     }

    //     return $query;
    // }

    public function getGuest($guests)
    {
    	$result = [];

        foreach($guests as $guest) {
            if($guest->main == 1) {
            	$result['fullname'] = $guest->first_name.' '. $guest->last_name;
            	$result['email'] = $guest->email;
            	$result['contact_number'] = $guest->contact_number;
            	$result['type'] = $guest->visitorType ? $guest->visitorType->name : null;
                $result['deleted_at'] = $guest->deleted_at;
            }
        }

        return $result;
    }

    public function fetchView(Request $request, $id = 0, $destination = null, $experience = null) {
        $item = null;

        if ($id != 0) {
        // if ($id) {
        	$item = Book::withTrashed()->findOrFail($id);
            $date = Carbon::parse($item->scheduled_at)->format('Y-m-d');
            $time = Carbon::parse($item->start_time)->format('H:i:s');
            $item->schedule = $date.' '.$time;
            $item->total_guests = $this->getGuests($item->guests()->where('main', false)->get());
            $item->is_invoice_approved = $item->invoice ? $item->invoice->is_approved : 0;
            $main = $item->guests()->where('main', true)->first();
            $item->main_id = $main->id;
            $item->main_birthdate = Carbon::parse($main->birthdate)->format('Y-m-d');
            $item->first_name = $main->first_name;
            $item->last_name = $main->last_name;
            $item->birthdate = $main->birthdate;
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

        $experiences = Destination::find($destination)->allocations;
        $nationalities = Countries::all();
        // $special_fees = Allocation::find($experience)->fees;
        $visitor_types = VisitorType::all();
        $blocked_dates = $this->blockedDates($destination);
	$genders = Gender::all();
	if($item->bookable_type ===  'App\Models\API\Masungi') {
		$add_ons = MasungiAddOn::all();
	}
        $fees = [];

        if($request->filled('allocation_id')) {
            $fees = ConservationFee::getFilteredFees($request->allocation_id);
        }

    	return response()->json([
    		'item' => $item,
            'managements' => Management::all(),
            'experiences' => $experiences,
            'nationalities' => $nationalities,
            // 'special_fees' => $special_fees,
            'visitor_types' => $visitor_types,
            'blocked_dates' => $blocked_dates,
            'genders' => $genders,
	    'fees' => $fees,
	    'add_ons' => $add_ons,
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
                // 'special_fee_id' => $item->special_fee_id,
                'conservation_fee_id' => $item->conservation_fee_id,
                'specialFeeImagePath' => $item->renderImagePath('special_fee_path'),
                'archiveUrl' => $item->renderArchiveUrl(),
            ]);
        }

        return $data;
    }
}
