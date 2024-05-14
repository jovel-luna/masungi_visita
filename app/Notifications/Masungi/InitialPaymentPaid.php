<?php

namespace App\Notifications\Masungi;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Helpers\NumberHelpers;
use Carbon\Carbon;

class InitialPaymentPaid extends Notification
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
    public function __construct($notification, $booking_details, $guest)
    {
        $this->booking_details = $booking_details;
        $this->notification = $notification;
        $this->notification->message = str_replace('[First Name]', $guest->first_name, $notification->message);
        $this->notification->message = str_replace('[Experience]', $booking_details->allocation->name, $notification->message);
        $this->notification->message = str_replace('(Date of Visit, Time)', Carbon::parse($booking_details->scheduled_at)->format('F d, Y') ." ". Carbon::parse($booking_details->start_time)->format('H:i A'), $notification->message);
        /* 4 is for 4 Banking Days */
        $this->notification->message = str_replace('(Month Day, Year)', Carbon::parse($booking_details->scheduled_at)->subWeekdays(4)->format('F d, Y'), $notification->message);
        $booking_deadline = Carbon::parse($booking_details->scheduled_at)->diffInWeekdays(Carbon::parse($booking_details->scheduled_at)->subWeekdays(4));
        $this->notification->message = str_replace('[number of days in words]', NumberHelpers::convert_number_to_words($booking_deadline), $notification->message);
        $this->notification->message = str_replace('number of days in numeric value', $booking_deadline, $notification->message);
        $this->notification->message = str_replace('[No. of guests]', $booking_details->guests->count(), $notification->message);
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
            //
        ];
    }
}
