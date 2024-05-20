<?php

namespace App\Notifications\Masungi;

use Illuminate\Support\Facades\Log;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Carbon\Carbon;

class FinalPaymentPaid extends Notification
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
        $this->notification->message = str_replace('(Date of Visit, Time)', Carbon::parse($booking_details->scheduled_at)->format('F d, Y') . " " . Carbon::parse($booking_details->start_time)->format('H:i A'), $notification->message);
        $this->notification->message = str_replace('[No. of guests]', $booking_details->guests->count(), $notification->message);
        $this->notification->message = str_replace('[QR Code]', $booking_details->renderImagePath('qr_code_path'), $notification->message);


        // $this->notification->message = str_replace('[IMAGE]', '<img src="https://dev.websiteprojectupdates.com/nmrha/wp-content/uploads/2024/04/logo.png">', $notification->message);

        // Extract src attribute value from [IMAGE src='src here'] tag
        // preg_match('/\[IMAGE src=\'(.*?)\'\]/', $notification->message, $matches);
        // $imageSrc = $matches[1] ?? '';

        // // Replace [IMAGE] tag with extracted src attribute value
        // $this->notification->message = preg_replace('/\[IMAGE src=\'(.*?)\'\]/', "<img src=\"$imageSrc\">", $notification->message);

        Log::info($this->booking_details);
        Log::info($this->notification);
        Log::info($this->notification->message);
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
