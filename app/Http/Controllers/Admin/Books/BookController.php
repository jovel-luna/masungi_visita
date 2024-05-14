<?php

namespace App\Http\Controllers\Admin\Books;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\Bookings\BookingStoreRequest;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

use App\Notifications\Frontliner\NewBookingNotification;
use App\Notifications\Reservation\BookingNotification;

use App\Models\Books\Book;
use App\Models\Books\AvailableSlots;
use App\Models\Guests\Guest;
use App\Models\Users\Management;
use App\Models\Emails\GeneratedEmail;
use App\Models\Times\MasungiTimeSlot;
use App\Models\Trails\MasungiTrail;
use App\Services\PushService;

use Carbon\Carbon;
use DB;
use Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($selectedDate, $destination, $experience, $destination_name)
    {
        $check_date = AvailableSlots::where('selected_date',$selectedDate)
                                            ->where('experience',$experience)
                                            ->first();

        $data = [
            'selectedDate' => $selectedDate,
            'is_available' => (empty($check_date)) ? 1 : 0,
            'date' => Carbon::parse($selectedDate)->format('F d, Y'),
            'destination' => $destination,
            'destination_name' => $destination_name,
            'experience' => $experience
        ];

        return view('admin.bookings.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($selectedDate, $destination, $experience, $destination_name)
    {
        return view('admin.bookings.create', [
            'selectedDate' => $selectedDate,
            'date' => Carbon::parse($selectedDate)->format('F d, Y'),
            'destination' => $destination,
            'destination_name' => $destination_name,
            'experience' => $experience
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BookingStoreRequest $request, $selectedDate, $destination, $experience, $destination_name)
    {
        DB::beginTransaction();
            $item = Book::store($request, null, null, $destination);
            // $item->invoice->create([

            // ]);

            $upload_path = null;
            if($request->special_fee_path) {
                $file = $request->special_fee_path;
                $filename = $file->getClientOriginalName();
                $path = 'public/special_fee';
                $upload_path = Storage::putFileAs($path, $file, $filename);
            }

            $item->guests()->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'birthdate' => $request->birthdate,
                    'gender' => $request->gender,
                    'nationality' => $request->nationality,
                    'contact_number' => $request->contact_number,
                    'emergency_contact_number' => $request->emergency_contact_number,
                    'email' => $request->email,
                    'visitor_type_id' => $request->visitor_type_id,
                    'special_fee_id' => $request->special_fee_id,
                    'special_fee_path' => $upload_path,
                    'main' => true
                ]);
            if($request->guest_first_name) {
                foreach($request->guest_first_name as $key => $guest) {
                    if($request->guest_special_fee_path[$key]) {
                        $file = $request->guest_special_fee_path[$key];
                        $filename = $file->getClientOriginalName();
                        $path = 'public/special_fee';
                        $upload_path = Storage::putFileAs($path, $file, $filename);
                    }

                    $item->guests()->create([
                        'first_name' => $request->guest_first_name[$key],
                        'last_name' => $request->guest_last_name[$key],
                        'birthdate' => $request->guest_birthdate[$key],
                        'email' => $request->guest_email[$key],
                        'gender' => $request->guest_gender[$key],
                        'nationality' => $request->guest_nationality[$key],
                        'visitor_type_id' => $request->guest_visitor_type[$key],
                        'special_fee_id' => $request->guest_special_fee_id[$key],
                        'special_fee_path' => $upload_path,
                    ]);
                }
            }
        DB::commit();

        $point_person = $item->guests()->where('main', true)->first();
        $qr_email = GeneratedEmail::where('notification_type', 'Booking notification')->first();
        $point_person->notify(new BookingNotification($item, $qr_email));
        $new_booking_frontliner = GeneratedEmail::where('notification_type', 'New booking notification')->first();

        $frontliners = Management::where('destination_id', $destination)->get();

        foreach ($frontliners as $key => $frontliner) {
            $frontliner->notify(new NewBookingNotification($item->destination, $item->alloction, $item, $point_person, $new_booking_frontliner));
        }
        $receiver = new PushService('New Reservation', 'A new reservation of visitor for '.Carbon::parse($request->scheduled_at)->format('M d, Y'). '.');
        $receiver->pushToMany($frontliners);

        $message = "You have successfully created a new reservation";
        $redirect = $item->renderShowUrl();
        return response()->json([
            'message' => $message,
            'redirect' => $redirect.'/'.$selectedDate.'/'.$destination.'/'.$experience.'/'.$destination_name,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $selectedDate,$destination, $experience, $destination_name)
    {
        $item = Book::withTrashed()->findOrFail($id);
        $submitUrl = route('admin.invoices.update', $item->invoice->id);
        $changeUrl = route('admin.bookings.set-available', $selectedDate,$destination);
        if(!$item->invoice->is_paid && $item->invoice->is_approved && $item->invoice->bank_deposit_slip) {
            $submitUrl = route('admin.invoices.approve.deposit', $item->invoice->id);
        }

        return view('admin.bookings.show', [
            'item' => $item,
            'changeUrl' => $changeUrl,
            'selectedDate' => $selectedDate,
            'date' => Carbon::parse($selectedDate)->format('F d, Y'),
            'destination' => $destination,
            'destination_name' => $destination_name,
            'experience' => $experience,
            'submitUrl' => $submitUrl,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookingStoreRequest $request, $id,  $selectedDate,$destination, $experience, $destination_name)
    {
        // return response()->json([
        //     'request' => $request
        // ]);
        $item = Book::withTrashed()->findOrFail($id);

        DB::beginTransaction();
            if($item->checkBookingAvailability($request->allocation_id, $request->scheduled_at, $request->start_time)) {
                $item = Book::store($request, $item, null, $destination);
            }

            // if($request->id) {
                $main = Guest::find($request->main_id);
                $main->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'birthdate' => $request->birthdate,
                    'gender' => $request->gender,
                    'nationality' => $request->nationality,
                    'contact_number' => $request->contact_number,
                    'emergency_contact_number' => $request->emergency_contact_number,
                    'email' => $request->email,
                    'visitor_type_id' => $request->visitor_type_id,
                    // 'special_fee_id' => $request->special_fee_id,
                    // 'conservation_fee_id' => $request->conservation_fee_id,
                ]);

                if($request->special_fee_path) {
                    $main->update([
                        'special_fee_path' => $this->uploadImage($request->special_fee_path)
                    ]);
                }
            // }
            $conversationFee = 0;

            if ($item->from_masungi_reservation) { // process masungi rules
                // Note: Fees are now being fetched from the database stored from Masungi Reservation
                // $fee = $item->allocation->conservationFees()->first();
                // $schedule = Carbon::parse($item->scheduled_at);
                // if ($schedule->isWeekday()) {
                //     $fee = $fee->weekday_fee;
                // } else {
                //     $fee = $fee->weekend_fee;
                // }
                $masungiTrail = MasungiTrail::where('name', $item->allocation->name)->first();
                // $guestCount = $masungiTrail->minimum_participant >= (count($request->guest_id) + 1) ?  $masungiTrail->minimum_participant : (count($request->guest_id) + 1);
                $guestCount = $masungiTrail->minimum_participant >= ($request->total_guest) ?  $masungiTrail->minimum_participant : ($request->total_guest);

                $invoice = $item->invoice;
                $payment_factor = $invoice->is_fullpayment ? 1 : 0.5;
                $invoice->conservation_fee = $invoice->conservation_fee_per_guest * $guestCount;
                $invoice->platform_fee = 0;
                if ($invoice->is_paypal_payment) {
                    if ($invoice->is_fullpayment) {
                        $invoice->platform_fee = ($invoice->conservation_fee * 0.044) + 15;
                    } else {
                        $invoice->platform_fee = (($invoice->conservation_fee / 2) * 0.044 + 15) * 2;
                    }
                }

                $invoice->sub_total = $invoice->conservation_fee + $invoice->platform_fee;
                $invoice->grand_total = $invoice->sub_total;


                $invoice->amount_settled = ($invoice->conservation_fee + $invoice->platform_fee) * $payment_factor ;
                $invoice->balance = !$invoice->is_fullpayment ? $invoice->amount_settled : 0;

                $invoice->save();
            }

            if($request->guest_first_name) {
                foreach($request->guest_first_name as $key => $guest) {
                    $guest = Guest::find($request->guest_id[$key]);
                    if($guest) {
                        $guest->update([
                            'first_name' => $request->guest_first_name[$key],
                            'last_name' => $request->guest_last_name[$key],
                            'birthdate' => $request->guest_birthdate[$key],
                            'email' => $request->guest_email[$key],
                            'gender' => $request->guest_gender[$key],
                            'contact_number' => $request->guest_contact_number[$key],
                            'emergency_contact_number' => $request->guest_emergency_contact_number[$key],
                            'nationality' => $request->guest_nationality[$key],
                            'visitor_type_id' =>  $request->guest_visitor_type ? $request->guest_visitor_type[$key] : '',
                            'special_fee_id' => $request->guest_special_fee_id ? $request->guest_special_fee_id[$key] : '',
                            'conservation_fee_id' => $request->guest_conservation_fee_id[$key],
                        ]);

                        if($request->guest_conservation_fee_id[$key] && !$item->from_masungi_reservation) {
                            $fee = $item->allocation->conservationFees()->where('id', $request->guest_conservation_fee_id[$key])->first();

                            $schedule = Carbon::parse($item->scheduled_at);
                            if($schedule->isWeekday()) {
                                $fee = $fee->weekday_fee;
                            } else {
                                $fee = $fee->weekend_fee;
                            }

                            $conversationFee += $fee;

                            $item->invoice->update([
                                'conservation_fee' => $conversationFee,
                                'sub_total' => $conversationFee + $item->invoice->platform_fee,
                                'grand_total' => $conversationFee + $item->invoice->platform_fee
                            ]);

                        }


                        $upload_path = null;
                        // dd() , $request->input('guest_special_fee_path')[1]);
                        if(isset($request->guest_special_fee_path[$key])) {
                            $guest->update([
                                'special_fee_path' => $this->uploadImage($request->guest_special_fee_path[$key])
                            ]);
                        }


                    } else {
                        // $upload_path = null;
                        // if($request->guest_special_fee_path[$key]) {
                        //     $file = $request->guest_special_fee_path[$key];
                        //     $filename = $file->getClientOriginalName();
                        //     $path = 'public/special_fee';
                        //     $upload_path = Storage::put($path, $file, $filename);
                        // }

                        // $item->guests()->create([
                        //         'first_name' => $request->guest_first_name[$key],
                        //         'last_name' => $request->guest_last_name[$key],
                        //         'birthdate' => $request->guest_birthdate[$key],
                        //         'email' => $request->guest_email[$key],
                        //         'gender' => $request->guest_gender[$key],
                        //         'nationality' => $request->guest_nationality[$key],
                        //         'visitor_type_id' => $request->guest_visitor_type[$key],
                        //         'special_fee_id' => $request->guest_special_fee_id[$key],
                        //         'special_fee_path' => $upload_path,
                        //     ]);

                        $item->guests()->create([
                            'main' => 0,
                            'first_name' => $request->guest_first_name[$key],
                            'last_name' => $request->guest_last_name[$key],
                            'birthdate' => $request->guest_birthdate[$key],
                            'email' => $request->guest_email[$key],
                            'gender' => $request->guest_gender[$key],
                            'contact_number' => $request->guest_contact_number[$key],
                            'emergency_contact_number' => $request->guest_emergency_contact_number[$key],
                            'nationality' => $request->guest_nationality[$key],
                            'visitor_type_id' =>  $request->guest_visitor_type ? $request->guest_visitor_type[$key] : '',
                            'special_fee_id' => $request->guest_special_fee_id ? $request->guest_special_fee_id[$key] : '',
                            'conservation_fee_id' => $request->guest_conservation_fee_id[$key],
                        ]);
                    }
                }
            }

        DB::commit();

        $message = "You have successfully updated the reservation";

        return response()->json([
            'message' => $message,
            'conversationFee' => $conversationFee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Destination  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = Book::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived the reservation",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Destination  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = Book::withTrashed()->findOrFail($id);
        
        if($item->checkBookingAvailability($item->allocation->id, $item->scheduled_at, $item->start_time)) {
            $item->unarchive();
        }
        
        // Return success message if booking timeslot is available
        return response()->json([
            'message' => "You have successfully restored the reservation",
        ]);
    }

    public function uploadImage($image) {
        $directory = 'special_fees';
        $extension = $image->getClientOriginalExtension() ? $image->getClientOriginalExtension() : 'jpg';
        $optimized_image = Image::make($image)->encode($extension);
        $width = $optimized_image->getWidth();
        $height = $optimized_image->getHeight();

        if ($width >= $height) {
            $optimized_image->resize(700, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $optimized_image->resize(null, 700, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $optimized_image->save();

        switch (config('web.filesystem')) {
            case 's3':
                    $root = null;
                break;

            default:
                    $image->store($directory, 'public');
                    $root = 'public/';
                break;
        }

        $file_path = $root . $directory . '/' . uniqid() . Str::random(40) . '.' . $extension;

        Storage::put($file_path, $optimized_image);

        return $file_path;

    }

    public function getAvailableSlots(Request $request) {
    // public function getAvailableSlots($selectedDate, $experience) {

        $date = $request->date;
        $trail_id = $request->trail_id;

        $slots = MasungiTimeSlot::getAvailable($date, $trail_id);

        if ($request->include_time) {
           $slots[] = ['id' => $request->include_time, 'name' => $request->include_time];
        }

       return $slots;    
    }

    public function makeAvailable(Request $request) {
    
            $date = $request->selectedDate;
            $experience = $request->experience;
            $is_available = 1;

            $slot = new AvailableSlots;

            $check_date = AvailableSlots::where('selected_date',$date)
                                            ->where('experience',$experience)
                                            ->first();

            if($check_date){ //remove from database
                AvailableSlots::where('selected_date',$date)
                                            ->where('experience',$experience)
                                            ->delete();
            }else{ //add to database
                $slot->experience = $experience;
                $slot->selected_date = $date;
                $slot->save();
                $is_available = 0;
            }

            $data = [
                'date' => $date,
                'experience' => $experience,
                'is_available' => $is_available
            ];
    
           return $data;    
        }

}
