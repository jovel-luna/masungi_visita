<?php

namespace App\Notifications\Masungi;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Carbon\Carbon;

class RemainingBalancePaid extends Notification
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
        $this->notification->message = str_replace('[date and time]', Carbon::parse($booking_details->scheduled_at)->format('F d, Y') ." ". Carbon::parse($booking_details->start_time)->format('H:i A'), $notification->message);
        $this->notification->message = str_replace('[no. of guests]', $booking_details->guests->count(), $notification->message);
        $this->notification->message = str_replace('[QR Code]', $booking_details->renderImagePath('qr_code_path'), $notification->message);
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
