<?php

namespace App\Notifications\Masungi;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Helpers\NumberHelpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class TrailRequestReminder extends Notification
{
    use Queueable;

    private $notification;
    private $booking_details;
    private $guest;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification, $booking_details, $invoice, $guest)
    {
        $this->booking_details = $booking_details;
        $this->notification = $notification;
        $this->notification->message = str_replace('[First Name]', $guest->first_name, $notification->message);
        
        $threeBankingDaysAfterDate = Carbon::parse($invoice->approved_at)->addWeekdays(3)->format('F j, Y');
        $fourBankingDaysBeforeDate = Carbon::parse($invoice->book->scheduled_at)->subWeekdays(4)->format('F j, Y');
        $payment = $invoice->conservation_fee;
        if (!$invoice->is_paid && !$invoice->is_fullpayment) {
            $payment = $invoice->conservation_fee / 2;
        }

        if(!$booking_details->first_trail_request_reminder_email_sent_at) {
            $this->notification->message = str_replace('[insert due date here]', $threeBankingDaysAfterDate, $notification->message);
        } else if ($booking_details->first_trail_request_reminder_email_sent_at) {
            $this->notification->message = str_replace('[insert due date here]', $fourBankingDaysBeforeDate, $notification->message);
        }

        $this->notification->message = str_replace('[INSERT AMOUNT TO BE SETTLED]', NumberHelpers::toMoney($payment, 'PHP'), $notification->message);

        $this->notification->message = str_replace('[Name of Experience]', $booking_details->allocation->name, $notification->message);
        $this->notification->message = str_replace('[00:00 AM/PM]', Carbon::parse($booking_details->start_time)->format('H:i A'), $notification->message);

        $this->notification->message = str_replace('[YYYY-MM-DD]', Carbon::parse($booking_details->scheduled_at)->format('Y-m-d'), $notification->message);
        $this->notification->message = str_replace('[Insert Number of Guests]', $booking_details->guests->count(), $notification->message);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
            return (new MailMessage)
                    ->bcc(['masungi.requests@gmail.com', 'trailvisits@masungigeoreserve.com'])
                    ->subject($this->notification->title)
                    ->line($this->notification->message)
                    ->markdown('vendor.mail.html.masungi-message', [
                        'message' => $this->notification->message
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->notification->title,
            'title' => $this->notification->message,
            'subject_id' => $notifiable->id,
            'subject_type' => get_class($notifiable),
        ];
    }
}