<?php

namespace App\Console\Commands\Masungi;

use Illuminate\Console\Command;

use App\Models\Invoices\Invoice;
use App\Models\Emails\GeneratedEmail;
use App\Notifications\Masungi\LapsedPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LapsedPaymentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:lapsed_payment';

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
        Log::info('TEST IF LAPSE PAYMENT WORKiNG');
        // Fetch invoices that have been completed one day before
        $invoices = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                         ->whereNotNull('second_trail_request_reminder_email_sent_at')
                         ->where('lapsed_payment_email_sent', 0)
                         ->whereYear('created_at', date("Y"))
                         ->whereNull('deleted_at');
                })->get();

                $invoicesCount = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                         ->whereNotNull('second_trail_request_reminder_email_sent_at')
                         ->where('lapsed_payment_email_sent', 0)
                         ->whereYear('created_at', date("Y"))
                         ->whereNull('deleted_at');
                })->count();
                Log::info("lapsed payment query count -" . $invoicesCount);  

        foreach ($invoices as $key => $invoice) {
            /* Old implementation 1: 3 is for 3 Banking Days */
            /*
                Condition 1: Visit Request has lapsed because the remaining balance has not been settled within the payment deadline, which is four (4) banking days before the date of visit.
                Condition 2: Check if invoice is paid for the remaining balance
                Condition 3: Check if invoice is not rejected
            */
            if(Carbon::parse($invoice->book->scheduled_at)->startOfDay()->subWeekdays(3) <= Carbon::now()
                && $invoice->is_firstpayment_paid
                && !$invoice->is_secondpayment_paid
                && !$invoice->is_fullpayment
                && !$invoice->is_paid
                && !$invoice->rejected_reason) {
                $lapsed_payment = GeneratedEmail::where('notification_type', GeneratedEmail::MASUNGI_LAPSED_PAYMENT)->where('email_to', GeneratedEmail::EMAIL_TO_MASUNGI)->first();
                $main = $invoice->book->guests->where('main', 1)->first();
                /* Send email notification to main guest */
                $main->notify(new LapsedPayment($lapsed_payment, $invoice->book, $main));
                /* Mark lapsed_payment_email_sent column as true */

                DB::table('books')
                ->where('id',$invoice->book_id)
                ->whereNotNull('second_trail_request_reminder_email_sent_at')
                ->where('lapsed_payment_email_sent', 0)
                ->whereNull('deleted_at')
                ->update([
                    'lapsed_payment_email_sent' => 1
                ]);

                // $invoice->book->update([
                //     'lapsed_payment_email_sent' => 1
                // ]);
            }
        }

    }
}
