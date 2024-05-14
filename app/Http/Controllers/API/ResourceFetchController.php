<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\API\FetchControllers\NationalityFetchController;
use App\Http\Controllers\API\FetchControllers\ExperienceFetchController;
use App\Http\Controllers\API\FetchControllers\VisitorTypeFetchController;
use App\Http\Controllers\API\FetchControllers\ReligionFetchController;
use App\Http\Controllers\API\FetchControllers\TrainingModuleFetchController;
use App\Http\Controllers\API\FetchControllers\SurveyFetchController;
use App\Http\Controllers\API\FetchControllers\AnnualIncomeFetchController;
use App\Http\Controllers\API\FetchControllers\FeedbackFetchController;

use App\Models\Fees\Fee;
use App\Models\Books\Book;
use App\Models\Guests\Guest;
use App\Models\Faqs\Faq;
use App\Models\Remarks\Remark;
use App\Models\Violations\Violation;
use App\Models\Users\Management;
use App\Models\BlockedDates\BlockedDate;
use App\Models\Genders\Gender;
use App\Models\CivilStatuses\CivilStatus;
use Webpatser\Countries\Countries;
use App\Models\TrainingModules\TrainingModule;
use App\Models\Destinations\Destination;
use Carbon\Carbon;

class ResourceFetchController extends Controller
{
    public function fetch(Request $request)
    {
        $user = $request->user();

        $fetch_nationalities = new NationalityFetchController($request);
        $fetch_experiences = new ExperienceFetchController($request);
        $fetch_types = new VisitorTypeFetchController($request);
        $fetch_religions = new ReligionFetchController($request);
        // $fetch_training_modules = new TrainingModuleFetchController($request);
        $fetch_surveys = new SurveyFetchController($request);
        $fetch_incomes = new AnnualIncomeFetchController($request);
        $fetch_feedbacks = new FeedbackFetchController($request);

        // $nationalities = $fetch_nationalities->fetch($request);
        $experiences = $fetch_experiences->fetch($request);
        $visitor_types = $fetch_types->fetch($request);
        $religions = $fetch_religions->fetch($request);
        $training_modules = TrainingModule::fetchItemAPI($user->destination_id);
        Log::info('User Logged In : '. $user);
        $surveys = $fetch_surveys->fetch($request);
        $incomes = $fetch_incomes->fetch($request);
        $feedbacks = $fetch_feedbacks->fetch($request);
        $faqs = Faq::where('type', 'FRONTLINER')->get();
        $remarks = Remark::all();
        $violations = Violation::all();
        $management = Management::where('role_id', 5)->where('destination_id', $request->user()->destination_id)->get();
        $bookings = $this->getBookings($request->user()->destination_id);
        $guests = $this->getGuests();
        $destination = auth()->guard('api')->user()->destination;
        $genders = Gender::all();
        $civil_status = CivilStatus::all();

        $selected_destination = Destination::find($user->destination_id)->allocations->map(function ($allocation) { 
            $allocation->fees = $allocation->conservationFees()->whereNotNull('visitor_type_id')->get();
         return $allocation;
        });

        $conservation_fees = [];
        foreach ($selected_destination as $allocation) {
            foreach ($allocation->fees as $fee) {
                array_push($conservation_fees, [
                    'id' => $fee->id,
                    'experience_id' => $fee->experience_id,
                    'display_name' => $fee->name,
                    'visitor_type_id' => $fee->visitor_type_id,
                    'special_fee_id' => $fee->special_fee_id,
                    'weekday_fee' => $fee->weekday_fee,
                    'weekend_fee' => $fee->weekend_fee,
                ]);
            }
        }

        $nationalities = [];
        $countries = Countries::orderBy('citizenship', 'asc')->get();
        foreach ($countries as $key => $country) {
            array_push($nationalities, [
                'citizenship' => $country->citizenship
            ]);
        }

        $blocked_dates = [];
        $blocks = BlockedDate::where('destination_id', $request->user()->destination_id)->get();
        
        foreach ($blocks as $block) {
            foreach ($block->dates as $date) {
                array_push($blocked_dates, [
                    'id' => $block->id,
                    'name' => $block->name,
                    'date' => $date->date
                ]);
            }
        }

        return response()->json([
            'user' => $user,
            'nationalities' => $nationalities,
            'experiences' => $experiences->original['items'],
            'visitor_types' => $visitor_types->original['items'],
            'religions' => $religions->original['items'],
            'training_modules' => $training_modules,
            'surveys' => $surveys->original['items'],
            'incomes' => $incomes->original['items'],
            'feedbacks' => $feedbacks->original['items'],
            'faqs' => $faqs,
            'remarks' => $remarks,
            'violations' => $violations,
            'management' => $management,
            'bookings' => $bookings,
            'guests' => $guests,
            'blocked_dates' => $blocked_dates,
            'destination' => $destination,
            'genders' => $genders,
            'civil_status' => $civil_status,
            'conservation_fees' => $conservation_fees,
        ]);
    }

    /**
     * Get the data of the dashboard of specific destination based on today's date
     */
    public function dashboard(Request $request)
    {
    	$today = Carbon::now();
    	// get current user login
    	$user = $request->user();

    	// get all booking based on current destination assigned for logged-in user
    	$bookings = Book::whereHas('invoice', function($query) {
            $query->where('is_paid', 1);
        })->where('destination_id', $user->destination->id)->whereDate('scheduled_at', $today);
    	
    	// total of guest today
    	$total['guest'] = $bookings->get()->sum('total_guest');

    	// total of groups today
    	$total['groups'] = $bookings->get()->count();
    	
        // get total checked in for walk in guest
        $checked_in_walkin['visitors'] = Book::whereHas('invoice', function($query) {
            $query->where('is_paid', 1);
        })->where('destination_id', $user->destination->id)->whereDate('scheduled_at', $today)->where('is_walkin', true)->whereDate('started_at', $today)->get()->sum('total_guest'); 
        $checked_in_walkin['groups'] = Book::whereHas('invoice', function($query) {
            $query->where('is_paid', 1);
        })->where('destination_id', $user->destination->id)->whereDate('scheduled_at', $today)->where('is_walkin', true)->whereDate('started_at', $today)->get()->count(); 

        // get total checked in for online in guest
        $total_checked_in['online_visitor'] = Book::whereHas('invoice', function($query) {
            $query->where('is_paid', 1);
        })->where('destination_id', $user->destination->id)->whereDate('scheduled_at', $today)->where('is_walkin', false)->whereDate('started_at', $today)->get()->sum('total_guest'); 
        $total_checked_in['online_group'] = Book::whereHas('invoice', function($query) {
            $query->where('is_paid', 1);
        })->where('destination_id', $user->destination->id)->whereDate('scheduled_at', $today)->where('is_walkin', false)->whereDate('started_at', $today)->get()->count(); 

        
        $total_checked_in['walk_in'] = Book::whereHas('invoice', function($query) {
            $query->where('is_paid', 1);
        })->where('destination_id', $user->destination->id)->whereDate('scheduled_at', $today)->where('is_walkin', true)->whereDate('started_at', $today)->get()->sum('total_guest'); 
        $total_checked_in['walk_in_group'] = Book::whereHas('invoice', function($query) {
            $query->where('is_paid', 1);
        })->where('destination_id', $user->destination->id)->whereDate('scheduled_at', $today)->where('is_walkin', true)->whereDate('started_at', $today)->get()->count(); 

    	// get the remaining capacity left for today 
    	$capacity_per_day = $user->destination->capacity_per_day;
    	$remaining = $capacity_per_day - $total['guest'];
    	$percentage = ($remaining / $capacity_per_day) * 100;

    	return response()->json([
    		'total' => $total,
    		'percentage' => round($percentage),
    		'remaining' => $remaining,
    		'total_checked_in' => $total_checked_in
    	]);
    }

    public function getBookings($id) 
    {
        $items = Book::where('destination_id', $id)->get();
        $data = [];
        foreach ($items as $item) {
            if($item->invoice->is_paid == 1) {
                array_push($data, [
                    'id' => $item->id,
                    'allocation_id' => $item->allocation_id,
                    'destination_id' => $item->destination_id,
                    'scheduled_at' => Carbon::parse($item->scheduled_at)->toDateString(),
                    'started_at' => $item->started_at ?? null,
                    'ended_at' => $item->ended_at ?? null,
                    'checked_in_at' => $item->checked_in_at,
                    're_scheduled_at' => Carbon::parse($item->re_scheduled_at)->toDateString(),
                    'status' => $item->getStatus(),
                    'agency_code' => $item->agency_code,
                    'total_guest' => $item->total_guest,
                    'payment_type' => $item->payment_type,
                    'payment_status' => $item->payment_status,
                    'is_walkin' => $item->is_walkin,
                    'qr_code_path' => $item->qr_code_path,
                    'qr_id' => $item->qr_id,
                    'group_remarks' => json_encode($item->groupRemarks),
                    'group_violations' => json_encode($item->groupViolations),
                    'guests' => json_encode($item->guests),
                    'main_contact' => json_encode($item->guests()->where('main', 1)->first()),
                    'allocation' => json_encode($item->allocation),
                    'created_at' => $item->created_at->format('j M Y h:i A'),
                    'is_walkin_label' => $item->is_walkin ? 'Walk-In' : 'Online',
                    'start_time' => $item->start_time,
                    'representative' => $item->representative ? json_encode($item->representative) : null,
                    'destination_representative_id' => $item->destination_representative_id,
                ]);
            }
        }

        return $data;
    }

    public function getGuests()
    {

        $items = Guest::all();
        $data = [];
        foreach ($items as $item) {
            array_push($data, [
                'id' => $item->id,
                'book_id' => $item->book_id,
                'special_fee_id' => $item->special_fee_id,
                'visitor_type_id' => $item->visitor_type_id,
                'main' => $item->main,
                'first_name' => $item->first_name,
                'gender' => $item->gender,
                'nationality' => $item->nationality,
                'last_name' => $item->last_name,
                'email' => $item->email,
                'birthdate' => $item->birthdate,
                'contact_number' => $item->contact_number,
                'emergency_contact_number' => $item->emergency_contact_number,
                'remarks' => $item->remarks,
                'signature_path' => empty(url($item->renderImagePath('signature_path'))) ? base64_encode(file_get_contents(url($item->renderImagePath('signature_path'))->path())) : null,
                'conservation_fee_id' => $item->conservation_fee_id,
            ]);
        }

        return $data;
    }
}
