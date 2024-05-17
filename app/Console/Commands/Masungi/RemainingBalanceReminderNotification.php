<?php

namespace App\Console\Commands\Masungi;

use Illuminate\Console\Command;

use App\Models\Invoices\Invoice;
use App\Models\Emails\GeneratedEmail;
use App\Notifications\Masungi\RemainingBalanceReminder;
use App\Notifications\Masungi\TrailRequestReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RemainingBalanceReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:remaining_balance_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a remaining balance reminder to notifiables in masungi';

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
        Log::info('TEST IF REMINDER BALANCE WORKiNG');
        // Fetch invoices that have been completed one day before
        $invoices = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                        ->whereNull('second_trail_request_reminder_email_sent_at')
                        ->where('expired_visit_request_email_sent', 0)
                        ->whereMonth('created_at', date("M"))
                        ->whereNull('deleted_at');
                })->whereNotNull('approved_at')->get();

                $invoicesCount = Invoice::whereHas('book', function($book) {
                    $book->where('bookable_type', 'App\Models\API\Masungi')
                        ->whereNull('second_trail_request_reminder_email_sent_at')
                        ->where('expired_visit_request_email_sent', 0)
                        ->whereMonth('created_at', date("M"))
                        ->whereNull('deleted_at');
                })->whereNotNull('approved_at')->count();

                Log::info("remaining balance query count -" . $invoicesCount);  

        foreach ($invoices as $key => $invoice) {
            /* 
                Conditions: 
                    If the User selected the 50% payment option.
                    If the User has settled their initial payment but not their remaining balance within the given deadline, which is four (4) banking days before the date of their visit.
                    If the Admin has not clicked the ‘Set as Fully Paid’ button in the Visita Backend. 
                    If this button has been clicked already by the Admin, the Reminder Email will no longer be received by the User.
            */
            if(Carbon::parse($invoice->book->scheduled_at)->startOfDay()->subWeekdays(4) <= Carbon::now()
                && !$invoice->is_fullpayment
                && $invoice->is_firstpayment_paid
               /*  && !$invoice->is_paid */
                ) {
                //$remaining_balance_reminder = GeneratedEmail::where('notification_type', GeneratedEmail::MASUNGI_REMAINING_BALANCE_REMINDER)->where('email_to', GeneratedEmail::EMAIL_TO_MASUNGI)->first(); // old code
                
                $this->sendEmail($invoice);

                DB::table('books')
                ->where('id',$invoice->book_id)
                ->whereNull('second_trail_request_reminder_email_sent_at')
                ->where('expired_visit_request_email_sent', 0)
                ->whereNull('deleted_at')
                ->update([
                    'second_trail_request_reminder_email_sent_at' => Carbon::now(),
                    'remaining_balance_email_sent' => 1
                ]);
                
                // $invoice->book->update([
                //     'second_trail_request_reminder_email_sent_at' => Carbon::now(),
                //     'remaining_balance_email_sent' => Carbon::now()
                // ]);
                
                Log::info('REMAINING BALANCE REMINDER SENT');
            }
        }

    }
}
