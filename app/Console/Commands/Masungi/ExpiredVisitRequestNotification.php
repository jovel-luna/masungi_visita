<?php

namespace App\Console\Commands\Masungi;

use Illuminate\Console\Command;

use App\Models\Invoices\Invoice;
use App\Models\Emails\GeneratedEmail;
use App\Notifications\Masungi\ExpiredVisitRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExpiredVisitRequestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:expired_visit_request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send lapsed payment notification to notifiables in masungi';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('TEST IF ExPIRED VISIT WORKiNG');
        // Fetch invoices that have been completed one day before
        $invoices = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                         ->whereNotNull('first_trail_request_reminder_email_sent_at')
                         ->where('expired_visit_request_email_sent', 0)
                         ->whereNull('deleted_at');
                })->whereNotNull('approved_at')->get();

        foreach ($invoices as $key => $invoice) {
            /* Old implementation: Send 5 banking days after approval */
            /*
                Condition 1: Check if the difference in days from when the trail request reminder email is 1
                    Requirement: The expired visit request email should be sent 24 hours after the trail request reminder has been sent
                Condition 2: Check if invoice is paid for the remaining balance; initial and full payment options will both fall under this condition
                Condition 3: Check if invoice is not rejected
            */


            if(Carbon::parse($invoice->approved_at)->startOfDay()->addDays(4) <= Carbon::now()
               //Carbon::parse($invoice->book->first_trail_request_reminder_email_sent_at)->diffInDays(Carbon::now()) === 1
               && !$invoice->is_firstpayment_paid
              /*  && !$invoice->is_paid */
               && !$invoice->rejected_reason) {
                /* Filter and fetch dynamic notification */
                $expired_visit_request = GeneratedEmail::where('notification_type', GeneratedEmail::MASUNGI_EXPIRED_VISIT_REQUEST)->where('email_to', GeneratedEmail::EMAIL_TO_MASUNGI)->first();
                $main = $invoice->book->guests->where('main', 1)->first();
                /* Send email notification to main guest */
                $main->notify(new ExpiredVisitRequest($expired_visit_request, $invoice->book, $main));
                /* Mark sent column as true */
                
                Log::info('Expired sent');
                DB::table('books')
                ->where('id',$invoice->book_id)
                ->whereNotNull('first_trail_request_reminder_email_sent_at')
                ->where('expired_visit_request_email_sent', 0)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => Carbon::now(),
                    'expired_visit_request_email_sent' => 1
                ]);

                // $invoice->book->update([
                //     'deleted_at' => Carbon::now(),
                //     'expired_visit_request_email_sent' => 1
                // ]);
            }
        }

    }
}
