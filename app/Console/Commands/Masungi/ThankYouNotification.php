<?php

namespace App\Console\Commands\Masungi;

use Illuminate\Console\Command;

use App\Models\Invoices\Invoice;
use App\Models\Emails\GeneratedEmail;
use App\Notifications\Masungi\ThankYou;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ThankYouNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:thank_you';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a thank you email to notifiables in masungi';

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
        Log::info('TEST IF THANKYOU WORKiNG');
        // Fetch invoices that have been completed one day before
        $invoices = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                        ->where('thank_you_email_sent', 0)
                        ->whereYear('created_at', date("Y"))
                        ->whereNull('deleted_at');
                })->whereNotNull('approved_at')->get();

                $invoicesCount = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                        ->where('thank_you_email_sent', 0)
                        ->whereYear('created_at', date("Y"))
                        ->whereNull('deleted_at');
                })->whereNotNull('approved_at')->count();

                Log::info("thank you notif query count -" . $invoicesCount);   

        /*
            Condition 1: Check if the difference in days between the date today and the scheduled date is 1
            Condition 2: Check if the date today is greater than the scheduled date
            Condition 3: Check if the invoice is paid
        */
        foreach ($invoices as $key => $invoice) {
            if(Carbon::parse($invoice->book->scheduled_at)->startOfDay()->addDay() <= Carbon::now()
                && $invoice->is_firstpayment_paid
                && $invoice->is_secondpayment_paid
                && $invoice->is_paid
                ) {
                /* Filter and fetch thank you notification */
                $thank_you_notification = GeneratedEmail::where('notification_type', GeneratedEmail::MASUNGI_THANK_YOU)->where('email_to', GeneratedEmail::EMAIL_TO_MASUNGI)->first();
                $main = $invoice->book->guests->where('main', 1)->first();
                /* Send email notification to main guest */
                $main->notify(new ThankYou($thank_you_notification, $main));
                /* Mark thank_you_email_sent column as true */
                // Log::info('THANKYOU SENT');
                
                // $invoice->book->update([
                //     'thank_you_email_sent' => 1
                // ]);

                DB::table('books')
                ->where('id',$invoice->book_id)
                ->where('thank_you_email_sent', 0)
                ->whereNull('deleted_at')
                ->update([
                    'thank_you_email_sent' => 1
                ]);
            }
        }

    }
}
