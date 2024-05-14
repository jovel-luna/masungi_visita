<?php

namespace App\Http\Controllers\API\Masungi\Invoices;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\API\Masungi\InvoiceStoreRequest;

use Illuminate\Validation\ValidationException;

use App\Models\Users\Management;
use App\Models\Invoices\Invoice;
use App\Models\Books\Book;
use App\Models\Books\AvailableSlots;
use App\Models\Allocations\Allocation;
use App\Models\Destinations\Destination;
use App\Models\Users\Admin;
use App\Models\Capacities\Capacity;
use App\Models\Emails\GeneratedEmail;

use App\Notifications\Reservation\BookingNotification;
use App\Notifications\Web\Bookings\NewBookingNotification as AdminBooking;
use App\Notifications\Frontliner\NewBookingNotification as FrontlinerBooking;
use App\Notifications\Admin\Paypal\AdminInvoicePaid;
use App\Notifications\Web\Paypal\UserInvoicePaid;
use App\Notifications\Masungi\InitialPaymentPaid;
use App\Notifications\Masungi\FinalPaymentPaid;
use App\Notifications\Masungi\ReservationReceived;

use DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
	private $masungi_user_id;

	public function validateRequest($request) {
		if(!$request->trail_name || !$request->start_time || !$request->scheduled_at ||
    		!$request->total_guest || !$request->trail_data || !$request->guests ||
    		!$request->conservation_fee || !$request->conservation_fee_per_guest || $request->transaction_fee === null ||
    		!$request->sub_total || !$request->grand_total || !$request->is_paypal_payment) {

			return 2; // ID of masungi user is required
		}
		return true;
	}

    public function store(Request $request, $user)
    {

        Log::info('Step Storing of Booking');

    	// $validation = $this->validateRequest($request);

    	// if($validation === 2) {
    	// 	return $validation;
    	// }

    	DB::beginTransaction();

    		$allocation = Allocation::where('name', $request->trail_name)->first();
    		$destination = Destination::where('name', 'Masungi Georeserve')->first();

    		// if(!$request->user_id) {
    		// 	return 1; // ID of masungi user is required
    		// }

    		if(!$destination) {
    			return 10; // 'destination' => "Masungi is not registered in system, contact Visita then add the Masungi in their system (Required to add name = Masungi)",
    		}

    		if(!$allocation) {

    			return 11; // 'trail' => "Trail is not registered in system, contact Visita and add the trail in under Masungi Destination"
    		}

            if($request->trail_name == 'Discovery Trail' && (!$request->add_on_id || $request->add_on_id == "" || $request->add_on_id == NULL)){
                
                return 14;
            }

            $guests = 0;

            if($guests = Book::where('bookable_type','App\Models\API\Masungi')
                            ->where('allocation_id', $allocation->id)
                            ->where('scheduled_at', $request->scheduled_at)
                            ->sum('total_guest')) { 
            }

            if($guests + $request->total_guest > $request->capacity) {
                return 12;
            }

            // Check if a reservation for the timeslot has been taken
            $reservationTaken = Book::whereHas('invoice', function($invoice) {
                                            $invoice->where('is_firstpayment_paid', true);
                                            $invoice->whereNull('rejected_reason');
                                        })
                                        ->where('allocation_id', $allocation->id)
                                        ->whereDate('scheduled_at', Carbon::parse($request->scheduled_at)->format('Y-m-d'))
                                        ->where('start_time', Carbon::parse($request->start_time)->format('H:i:s'))
                                        ->exists();

            if($reservationTaken) {
                return 13; // Booking for this scheduled timeslot already exists
            }

    		$book = $user->books()->create([
    			'allocation_id' => $allocation->id,
    			'destination_id' => $destination->id,
	    		'start_time' => $request->start_time,
	    		'scheduled_at' => $request->scheduled_at,
	    		'total_guest' => $request->total_guest,
                'is_walkin' => false,
                'from_masungi_reservation' => true,
                'trail_data' => json_encode($request->trail_data),
                'other_data' => $request->other_data ? json_encode($request->other_data) : null,
		// 'masungi_user_id' => $request->user_id,
		'add_on_id' => $request->add_on_id,
    		]);
    		foreach ($request->guests as $key => $guest) {
                $book->guests()->create([
	    			'main' => $guest['main'] == '1' ? 1 : 0,
	    			'first_name' => $guest['first_name'],
	    			'last_name' => $guest['last_name'],
                    // 'gender' => $guest['gender'],
	    			'gender' => 'Male',
	    			'nationality' => $guest['country'],
	    			'emergency_contact_number' => $guest['contact_number'],
	    			'contact_number' => $guest['contact_number'],
	    			'email' => $guest['email'],
	    			'birthdate' => $guest['birthday'],
	    		]);
	    	}
            $firstpayment = false;
            $secondpayment = true;

            if(!$request->is_fullpayment) {
                $firstpayment = false;
                $secondpayment = false;
            }

	    	$invoice = $user->invoices()->create([
	    		'book_id' => $book->id,
	    		'conservation_fee' => $request->conservation_fee,
	    		'conservation_fee_per_guest' => $request->conservation_fee_per_guest,
	    		'platform_fee' => 0,
	    		'transaction_fee' => $request->transaction_fee,
	    		'sub_total' => $request->sub_total,
	    		'grand_total' => $request->grand_total,
	    		'is_paypal_payment' => $request->is_paypal_payment,
	    		'reference_code' => $request->grand_total.$this->generateReferenceCode().'MSNG',
                'is_fullpayment' => $request->is_fullpayment,
                'amount_settled' => $request->amount_settled,
                'balance' => $request->balance,
                'is_firstpayment_paid' => $firstpayment,
                'is_secondpayment_paid' => $secondpayment,
	    	]);



            Log::info('Frontliner sent!');

        DB::commit();


        $qr_email = GeneratedEmail::where('notification_type', 'Booking notification')->first();
        $new_booking_frontliner = GeneratedEmail::where('notification_type', 'New booking notification')->first();
        // $main->notify(new BookingNotification($book, $qr_email));
        Log::info('Main contact person sent!');
        /*$admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new AdminBooking($book->destination, $book->allocation, $book, $main, $new_booking_frontliner));
        }*/
        $frontliners = Management::where('destination_id', $book->destination->id)->get();

        if($main = $invoice->book->guests->where('main', 1)->first()) {
            $reservation_received = GeneratedEmail::where('notification_type', GeneratedEmail::MASUNGI_RESERVATION_RECEIVED)->where('email_to', GeneratedEmail::EMAIL_TO_MASUNGI)->first();
            $main->notify(new ReservationReceived($reservation_received, $invoice->book, $invoice, $main));
        }

        foreach ($frontliners as $key => $frontliner) {
            $frontliner->notify(new FrontlinerBooking($book->destination, $book->allocation, $book, $main, $new_booking_frontliner));
        }

    	return 200;
    }

    public function generateReferenceCode()
    {
    	$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

	    return substr(str_shuffle($str_result),
	                       0, 20);
    }

    // public function showReservations($masungi_user_id, $user)
    public function showReservations($user)
    {
    	// $this->masungi_user_id = $masungi_user_id;
    	$result = [];
    	$invoices = $user->invoices()->whereHas('book', function($book) {
    		$book->where('masungi_user_id', $this->masungi_user_id);
    	})->get();
        $invoices = [];

    	foreach ($invoices as $invoice) {
    		array_push($result, [
    			'trail' => $invoice->book->allocation->name,
    			'scheduled_at' => $invoice->book->scheduled_at->format('m/d/Y'),
    			'total_guest' => $invoice->book->total_guest,
    			'status_label' => $invoice->renderStatusLabel(),
    			'grand_total' => $invoice->grand_total,
    			'sub_total' => $invoice->sub_total,
    			'conservation_fee' => $invoice->conservation_fee,
    			'conservation_fee_per_guest' => $invoice->conservation_fee_per_guest,
    			'transaction_fee' => $invoice->transaction_fee,
    			'is_paid' => $invoice->is_paid ? true : false,
    			'is_approved' => $invoice->is_approved ? true : false,
    			'is_paypal_payment' => $invoice->is_paypal_payment ? true : false,
    			'reference_code' => $invoice->reference_code,
    			'id' => $invoice->id,
    			'showImgTag' => $invoice->bank_deposit_slip ?? false,
    			'deposit_slip' => !$invoice->is_paypal_payment ? url('storage/'.$invoice->bank_deposit_slip) : null,
    			'guests' => $invoice->book->getGuests(),
    			'trail_data' => json_decode($invoice->book->trail_data),
    			'other_data' => json_decode($invoice->book->other_data),
    		]);
    	}

    	return response()->json([
    		'items' => $result
    	]);
    }

    public function getAvailableMondaySlots()
    {
        $slots = AvailableSlots::all();

        return response()->json(['slots' => $slots]);
    }

    public function checkIfAvailable($request)
    {
        $date = AvailableSlots::where('selected_date',$request['date'])
                                ->where('experience',$request['experience'])
                                ->first();

        return response()->json(['date' => $date]);
    }

    // Unused due to client request
    // public function paypalPaid($request)
    // {

    //     Log::info('Step Payment');
    //     Log::info($request);
    //     Log::info($request['reference_code']. '----'.$request['payment_code']);

    // 	if(!$request['reference_code']) {
    // 		return 3; // reference code is required
    // 	}

    //     if(strpos($request['reference_code'], '*secondpayment')) {
    //         $request['reference_code'] = str_replace('*secondpayment','',$request['reference_code']);
    //         Log::info('Second payment new reference_code : '. $request['reference_code']);
    //     }

    //     Log::info('Condition pass');

    //     $invoice = Invoice::where('reference_code', $request['reference_code'])->first();
    //     Log::info('Invoice is get');

	// 	$admins = Admin::all();
    //     Log::info('Admins get');
	// 	$main = $invoice->book->guests->where('main', true)->first();
    //     Log::info('main guest get');

    // 	DB::beginTransaction();
    //         Log::info($invoice);
    //         $invoice->update([
    //             'payment_code' => $request['payment_code'],
    //         ]);

    //         if($invoice->is_fullpayment == 1) {
    //             $invoice->is_paid = true;
    //             $invoice->is_firstpayment_paid = true;
    //             $invoice->is_secondpayment_paid = true;
    //             $main->notify(new UserInvoicePaid($invoice));
    //             $main->notify(new FinalPaymentPaid());
    //             Log::info('Email sent to user');
    //             foreach ($admins as $admin) {
    //                $admin->notify(new AdminInvoicePaid($invoice));
    //             }
    //             Log::info('Email sent to admin');
    //         } elseif ($invoice->is_fullpayment == 0 && $invoice->is_firstpayment_paid == 0) {
    //             $invoice->is_firstpayment_paid = true;
    //             $main->notify(new InitialPaymentPaid($invoice));
    //         } elseif ($invoice->is_fullpayment == 0 && $invoice->is_firstpayment_paid == 1 && $invoice->is_secondpayment_paid == 0) {
    //             $invoice->is_secondpayment_paid = true;
    //             $invoice->is_paid = true;
    //             $main->notify(new UserInvoicePaid($invoice));
    //             $main->notify(new FinalPaymentPaid());
    //             Log::info('Email sent to user');
    //             foreach ($admins as $admin) {
    //                $admin->notify(new AdminInvoicePaid($invoice));
    //             }
    //             Log::info('Email sent to admin');
    //         }

    //         $invoice->save();
    //         // $invoice->is_paid = true;
    //         // $invoice->payment_code = $request['payment_code'];
    //         // $invoice->save();
    //         Log::info('invoice update');


    // 	DB::commit();
    //     Log::info('DB commit');

    // 	return 200;
    // }

    public function getAvailability($request)
    {
        $trail = $request->trail_name;
        $date = Carbon::parse($request->date);
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();
        $time = $request->start_time;

        $result = Invoice::whereHas('book', function($book) use($trail, $startOfDay, $endOfDay, $time) {
                    $book->whereBetween('scheduled_at', [$startOfDay, $endOfDay])
                        ->where('start_time', Carbon::parse($time)->format('H:i:s'))
                        ->whereHas('allocation', function($allocation) use($trail) {
                            $allocation->where('name', $trail);
                        })
                        ->whereNull('deleted_at');
                        // Check if the booking is active
                        #->whereHas('invoice', function($invoice) {
                        #    $invoice->whereNull('deleted_at');
			#});
			//->whereHas('invoice');
                })->whereNull('deleted_at')
		->exists() ? 'false' : 'true';
	#\Log::info("$date - $time  $result");
	#\Log::info($result);
	return $result;
    }

    // public function getAvailabilityy($request) {
    //     $allocation = Allocation::where('name', $request->trail_name)->first();
    //     $capacity = Capacity::where('allocation_id', $allocation->id)->first()->online;
    //     $time = $request->start_time;
    //     $schedule_date = $request->date;

    //     // $invoices = Invoice::where('is_paid', true)->get();
    //     $invoices = Invoice::all();

    //     $count = 0;

    //     $canShow = 'true';
    //     $sample = [];
    //     foreach ($invoices as $key => $invoice) {
    //         if($invoice->is_fullpayment && $invoice->is_paid) {
    //             if($invoice->book->allocation->id === $allocation->id && $invoice->book->scheduled_at == Carbon::parse($schedule_date) && $invoice->book->start_time == Carbon::parse($time)->format('H:i:s')) {
    //                 $count += 1;
    //             }
    //         } elseif(!$invoice->is_fullpayment && $invoice->is_firstpayment_paid) {
    //             if($invoice->book->allocation->id === $allocation->id && $invoice->book->scheduled_at == Carbon::parse($schedule_date) && $invoice->book->start_time == Carbon::parse($time)->format('H:i:s')) {
    //                 $count += 1;
    //             }
    //         }

    //         // array_push($sample, [
    //         //     'invoice' => $invoice->book
    //         // ]);
    //         // $count += $invoice->book->where('allocation_id', $allocation->id)->count();

    //     }

    //     if($capacity <= $count) {
    //         $canShow = 'false';
    //     }

    //     return $canShow;
    // }
}
