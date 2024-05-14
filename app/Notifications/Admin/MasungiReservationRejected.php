<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Carbon\Carbon;

class MasungiReservationRejected extends Notification
{
    use Queueable;

    public $notification;
    private $invoice;
    public $guest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification, $invoice, $guest)
    {
        $this->notification = $notification;
        $this->invoice = $invoice;
        $this->notification->message = str_replace('[Experience Name]', $invoice->book->allocation->name, $notification->message);
        $this->notification->message = str_replace('[Date and time]', Carbon::parse($invoice->book->scheduled_at)->format('F d, Y') ." ". Carbon::parse($invoice->book->start_time)->format('H:i A'), $notification->message);
        $this->notification->message = str_replace('[First Name]', $guest->first_name, $notification->message);
        $this->notification->message = str_replace('[reason indicated in textbox in backend]', strip_tags($invoice->rejected_reason), $notification->message);
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
            'message' => $this->notification->message,
            'title' => $this->notification->title,
            'subject_id' => $notifiable->id, 
            'subject_type' => get_class($notifiable),
        ];
    }
}
