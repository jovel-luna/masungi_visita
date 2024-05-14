<?php

namespace App\Notifications\Web\Paypal;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Carbon\Carbon;

class UserInvoicePaid extends Notification
{
    use Queueable;

    private $invoice;
    public $qr_path;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->qr_path = "Download the QR here : ".url($this->invoice->book->renderImagePath('qr_code_path')).".";
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
        $total = floatval($this->invoice->grand_total) + floatval($this->invoice->transaction_fee);
        return (new MailMessage)
                ->subject('Reservation Invoice Paid')
                ->greeting('Hello ' . $notifiable->renderName() . ',')
                ->line('Invoice Paid')
                ->line('Invoice Details : ')
                ->line('Reference # : '. $this->invoice->reference_code)
                ->line('Conservation Fee : ₱'. $this->invoice->conservation_fee)
                ->line('Platform Fee : ₱'. $this->invoice->platform_fee)
                ->line('Sub Total : ₱'. $this->invoice->sub_total)
                ->line('Transaction Fee : ₱'. $this->invoice->transaction_fee)
                ->line('Total : ₱'. $total)
                ->line('Reservation Details : ')
                ->line('Destination : '. $this->invoice->book->destination->name)
                ->line('Experience : '. $this->invoice->book->allocation->name)
                ->line('Number of Guests : '. $this->invoice->book->total_guest)
                ->line('Schedule : '. $this->invoice->book->scheduled_at->format('M d, Y'))
                ->line('Start time of the visit : '. Carbon::createFromFormat('H:i:s', $this->invoice->book->start_time)->format('h:i A'))
                ->line('<img src="'.$this->qr_path.'"/>')
                ->line($this->qr_path)
                ->line('Thank you!');
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
            'message' => 'Invoice Paid for Reference # : '. $this->invoice->reference_code,
            'title' => 'Invoice Paid',
            'subject_id' => $notifiable->id,
            'subject_type' => get_class($notifiable),
        ];
    }
}
