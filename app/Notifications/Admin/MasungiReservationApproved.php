<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Helpers\NumberHelpers;
use Carbon\Carbon;

class MasungiReservationApproved extends Notification
{
    use Queueable;

    private $invoice;
    private $notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice, $notification, $guest)
    {
        $this->invoice = $invoice;
        $this->notification = $notification;

        $masungi_url = config('masungi.url');
        $payment = $invoice->grand_total;
        $reference_code = $invoice->reference_code;
        $due_date = Carbon::parse($invoice->book->approved_at)->addWeekdays(3)->format('F d, Y');
        $payment = $invoice->conservation_fee;
        if (!$invoice->is_paid && !$invoice->is_fullpayment) {
            $payment = $invoice->conservation_fee / 2;
        }
        if ($invoice->is_firstpayment_paid && !$invoice->is_secondpayment_paid && !$invoice->is_paid && !$invoice->is_fullpayment) {
            /* $payment = $invoice->balance; */
            $reference_code = $invoice->reference_code . '*secondpayment';
            $due_date =  Carbon::parse($invoice->book->scheduled_at)->subWeekdays(4)->format('F d, Y');
        } elseif (!$invoice->is_firstpayment_paid && !$invoice->is_secondpayment_paid && !$invoice->is_paid && !$invoice->is_fullpayment) {
            /* $payment = $invoice->amount_settled; */
        }
        // $paypal_value = $invoice->is_paypal_payment ? $payment + ($payment * .044) + 15 : $payment;
        $paypal_value = $payment + ($payment * .044) + 15;
        // dd($paypal_value);
        $payment_link = '<a href="' . $masungi_url . '/payment/' . $guest->renderName() . '/' . $reference_code . '/' . $paypal_value . '">click here</a>';
        // dd($payment_link);
        // $this->notification->message = str_replace('click here', $payment_link, $notification->message);
        $this->notification->message = str_replace('[Paypal Link]', $payment_link, $notification->message);
        $this->notification->message = str_replace('[First Name]', $guest->first_name, $notification->message);
        $this->notification->message = str_replace('[PHP 00,000 (50%/100% of total conservation fee).]', NumberHelpers::toMoney($payment, 'PHP'), $notification->message);

        
        // https://masungigeoreserve.com/payment/Air%20Air/10992vu21mK7PhOfIXwFCWgzDMSNG/5496
        // $this->notification->message = str_replace('[Paypal Link]', 'https://masungigeoreserve.com/payment/Air%20Air/10992vu21mK7PhOfIXwFCWgzDMSNG/'.$payment , $notification->message);
        // $this->notification->message = str_replace('[Paypal Link]', '<a href="https://masungigeoreserve.com/payment/Air%20Air/10992vu21mK7PhOfIXwFCWgzDMSNG/'.$paypal_value.'">click here</a>', $notification->message);    
        $this->notification->message = str_replace('[Experience Name]', $invoice->book->allocation->name, $notification->message);
        $this->notification->message = str_replace('[# of guests]', $invoice->book->guests->count(), $notification->message);
        $this->notification->message = str_replace('(Month day, year, TIME)', Carbon::parse($invoice->book->scheduled_at)->format('F d, Y') . " " . Carbon::parse($invoice->book->start_time)->format('H:i A'), $notification->message);
        $this->notification->message = str_replace('[Month Day, Year]', $due_date, $notification->message);
        $this->notification->message = str_replace('(Month Day, Year)', Carbon::parse($invoice->book->scheduled_at)->subWeekdays(4)->format('F d, Y') . ' 11:59 PM', $notification->message);
        $this->notification->message = str_replace('[Month day, Year 00:00 AM/PM]', $due_date .  " 11:59 PM", $notification->message);
    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
            ->markdown('emails.orders.shipped', [
                'url' => $this->orderUrl,
            ]);
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
        $notification = $this->notification;

        $mailMessage = new MailMessage;
        $mailMessage
            ->bcc(['masungi.requests@gmail.com', 'trailvisits@masungigeoreserve.com'])
            ->subject($notification->title)
            ->line($notification->message)
            ->markdown('vendor.mail.html.masungi-message', [
                'message' => $notification->message
            ]);

        return $mailMessage;
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
