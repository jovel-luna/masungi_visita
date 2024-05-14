<?php

namespace App\Console\Commands\Masungi;

use Illuminate\Console\Command;


use Illuminate\Support\Facades\Validator;
use App\Models\Invoices\Invoice;
use App\Models\Emails\GeneratedEmail;
use App\Notifications\Masungi\TrailRequestReminder;
use Carbon\Carbon;

use App\Rules\less_than_5_banking_days;
use App\Rules\more_than_5_banking_days;
use App\Rules\more_than_10_banking_days;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TrailRequestReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:trail_request_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a trail request reminder to notifiables in masungi';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->handle();
    }

    public function sendEmail($invoice)
    {
        /* Filter and fetch dynamic notification */
        $trail_request_reminder = GeneratedEmail::where('notification_type', GeneratedEmail::MASUNGI_TRAIL_REQUEST_REMINDER)->where('email_to', GeneratedEmail::EMAIL_TO_MASUNGI)->first();
        $main = $invoice->book->guests->where('main', 1)->first();
        /* Send email notification to main guest */
        $main->notify(new TrailRequestReminder($trail_request_reminder, $invoice->book, $invoice, $main));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('TEST IF TRAIL WORKiNG 2');
        // Fetch invoices that have been completed one day before
        $invoices = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                         ->whereNull('first_trail_request_reminder_email_sent_at')
                         ->whereNull('deleted_at')
                         ->where('expired_visit_request_email_sent', 0);
                })->whereNotNull('approved_at')->get();

        
        foreach ($invoices as $key => $invoice) {
            /*
                Condition 1: Check if current day is the third (3) day of banking after the reservation was approved
                Condition 2: Check if the first_trail_request_reminder_email_sent_at column is not filled yet
                Condition 3: Check if invoice is paid for the remaining balance; initial and full payment options will both fall under this condition
                Condition 4: Check if invoice is not rejected
            */

            // $validate_less_3 =  Validator::make($invoice, [
            //     'created_at' => new less_than_5_banking_days,
            
            // ]);

            $validate_less_3 = Validator::make(
                ['created_at' => $invoice->created_at], ['created_at' => new less_than_5_banking_days($invoice->book_id),
            ]);

            $validate_more_5 = Validator::make(
                ['created_at' => $invoice->created_at], ['created_at' => new more_than_5_banking_days,
            ]);

            $validate_more_10 = Validator::make(
                ['created_at' => $invoice->created_at], ['created_at' => new more_than_10_banking_days,
            ]);

            
            if($validate_less_3->passes()){
                if(
                    //Carbon::parse($invoice->approved_at)->startOfDay()->diffInWeekDays(Carbon::now()->startOfDay()) === 3
                    Carbon::parse($invoice->approved_at)->startOfDay()->addHours(12) <= Carbon::now()
                    && !$invoice->is_firstpayment_paid
                    && !$invoice->rejected_reason
                ) {
                    $this->sendEmail($invoice);
                    /* Mark sent column as true */
    
                    DB::table('books')
                    ->where('id',$invoice->book_id)
                    ->whereNull('first_trail_request_reminder_email_sent_at')
                    ->where('expired_visit_request_email_sent', 0)
                    ->whereNull('deleted_at')
                    ->update([
                        'first_trail_request_reminder_email_sent_at' => Carbon::now()
                    ]);
                }
            }
            if($validate_more_5->passes()){
                if(
                    //Carbon::parse($invoice->approved_at)->startOfDay()->diffInWeekDays(Carbon::now()->startOfDay()) === 3
                    Carbon::parse($invoice->approved_at)->startOfDay()->addDays(3) <= Carbon::now()
                    && !$invoice->is_firstpayment_paid
                    && !$invoice->rejected_reason
                    && Carbon::now()->isStartOfDay()
                ) {
                    $this->sendEmail($invoice);
                    /* Mark sent column as true */
    
                    DB::table('books')
                    ->where('id',$invoice->book_id)
                    ->whereNull('first_trail_request_reminder_email_sent_at')
                    ->where('expired_visit_request_email_sent', 0)
                    ->whereNull('deleted_at')
                    ->update([
                        'first_trail_request_reminder_email_sent_at' => Carbon::now()
                    ]);
                }

             }


            // Log::info($validate_less_3);

            // if(
            //     //Carbon::parse($invoice->approved_at)->startOfDay()->diffInWeekDays(Carbon::now()->startOfDay()) === 3
            //     Carbon::parse($invoice->approved_at)->startOfDay()->addWeekdays(3) <= Carbon::now()
            //     && !$invoice->is_firstpayment_paid
            //     && !$invoice->rejected_reason
            // ){
                
            //     $this->sendEmail($invoice);
            //     /* Mark sent column as true */

            //     DB::table('books')
            //     ->where('id',$invoice->book_id)
            //     ->whereNull('first_trail_request_reminder_email_sent_at')
            //     ->where('expired_visit_request_email_sent', 0)
            //     ->whereNull('deleted_at')
            //     ->update([
            //         'first_trail_request_reminder_email_sent_at' => Carbon::now()
            //     ]);
                
            //     // $invoice->book->update([
            //     //     'first_trail_request_reminder_email_sent_at' => Carbon::now()
            //     // ]);
            // }
            /*
                Condition 1: Check if current day is 4 banking days behind the scheduled date
                Condition 2: Check if the date today is before the scheduled date
                Condition 3: Check if the second_trail_request_reminder_email_sent_at column is not filled yet
                Condition 4: Check if the first_trail_request_reminder_email_sent_at column is filled
                Condition 5: Check if invoice is paid for the remaining balance
                Condition 5: Check if invoice is not rejected
            */
            
            // transfered to another command for remaining balance
            /* else if(
                Carbon::parse($invoice->book->scheduled_at)->startOfDay()->diffInWeekDays(Carbon::now()->startOfDay()) === 4
                && Carbon::now()->startOfDay()->isPast(Carbon::parse($invoice->book->scheduled_at)->startOfDay())
                && !$invoice->book->second_trail_request_reminder_email_sent_at
                && $invoice->book->first_trail_request_reminder_email_sent_at
                && !$invoice->isPaid()
                && !$invoice->rejected_reason
            ) {
                $this->sendEmail($invoice);
                // Mark sent column as true 
                $invoice->book->update([
                    'second_trail_request_reminder_email_sent_at' => Carbon::now()
                ]);
            } */
        }

    }
}
