<?php

namespace App\Models\Emails;

use App\Helpers\StringHelpers;

use App\Extenders\Models\BaseModel as Model;
use App\Models\Allocations\Allocation;

class GeneratedEmail extends Model
{
	const BOOKING_NOTIFICATION = 'Booking notification';
	const GENERATED_QR_NOTIFICATION = 'Generating QR notification';
	const NEW_BOOKING_NOTIFICATION = 'New booking notification';
	const RESERVATION_REJECTED = 'Rejected reservation';
    const RESERVATION_APPROVED = 'Approved reservation';
	const RESERVATION_RECEIVED = 'Reservation Received';
    const MASUNGI_RESERVATION_RECEIVED = 'Masungi: Reservation Received';
    const MASUNGI_RESERVATION_APPROVED = 'Masungi: Reservation Approved';
    const MASUNGI_RESERVATION_REJECTED = 'Masungi: Reservation Rejected';
    const MASUNGI_INITIAL_PAYMENT = 'Masungi: Initial Payment Confirmation';
    const MASUNGI_REMAINING_BALANCE_CONFIRMATION = 'Masungi: Remaining Balance Confirmation';
    const MASUNGI_FULL_PAYMENT = 'Masungi: Full Payment Confirmation';
    const MASUNGI_EXPIRED_VISIT_REQUEST = 'Masungi: Expired Visit Request';
    const MASUNGI_LAPSED_PAYMENT = 'Masungi: Lapsed Payment';
    const MASUNGI_TRAIL_REQUEST_REMINDER = 'Masungi: Trail Request Reminder';
    const MASUNGI_REMAINING_BALANCE_REMINDER = 'Masungi: Remaining Balance Reminder';
    const MASUNGI_THANK_YOU = 'Masungi: Thank you';

    const EMAIL_TO_VISITA = 'VISITA';
    const EMAIL_TO_MASUNGI = 'MASUNGI';

    public function experience()
    {
        return $this->belongsTo(Allocation::class, 'experience_id');
    }


    /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['title', 'notification_type', 'message', 'email_to', 'experience_id'])
    {
        $vars = $request->only($columns);

        if (!$item) {
            $item = static::create($vars);
        } else {
            $item->update($vars);
        }

        return $item;
    }

    /**
     * @Getters
     */
    public static function getTypes() {
        return [
            ['value' => static::BOOKING_NOTIFICATION, 'label' => 'Booking notification', 'class' => 'bg-info'],
            ['value' => static::GENERATED_QR_NOTIFICATION, 'label' => 'Generating QR notification', 'class' => 'bg-success'],
            ['value' => static::NEW_BOOKING_NOTIFICATION, 'label' => 'New booking notification', 'class' => 'bg-warning'],
            ['value' => static::RESERVATION_REJECTED, 'label' => 'Rejected reservation', 'class' => 'bg-warning'],
            ['value' => static::RESERVATION_APPROVED, 'label' => 'Approved reservation', 'class' => 'bg-warning'],
            ['value' => static::RESERVATION_RECEIVED, 'label' => 'Reservation Received', 'class' => 'bg-warning'],
            ['value' => static::MASUNGI_RESERVATION_RECEIVED, 'label' => 'Reservation Received', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_RESERVATION_APPROVED, 'label' => 'Reservation Approved', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_RESERVATION_REJECTED, 'label' => 'Reservation Rejected', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_INITIAL_PAYMENT, 'label' => 'Initial Payment Confirmation', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_REMAINING_BALANCE_CONFIRMATION, 'label' => 'Remaining Balance Confirmation', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_FULL_PAYMENT, 'label' => 'Full Payment Confirmation', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_EXPIRED_VISIT_REQUEST, 'label' => 'Expired Visit Request', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_LAPSED_PAYMENT, 'label' => 'Lapsed Payment', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_TRAIL_REQUEST_REMINDER, 'label' => 'Trail Request Reminder', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_REMAINING_BALANCE_REMINDER, 'label' => 'Remaining Balance Reminder', 'class' => 'bg-info'],
            ['value' => static::MASUNGI_THANK_YOU, 'label' => 'Thank you', 'class' => 'bg-info'],
        ];
    }

    public static function getEmailTypes() {
        return [
            ['value' => static::EMAIL_TO_VISITA, 'label' => 'Visita'],
            ['value' => static::EMAIL_TO_MASUNGI, 'label' => 'Masungi'],
        ];
    }

    /**
     * @Render
     */
    public function renderShowUrl($prefix = 'admin') {
        return route($prefix . '.generated-emails.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.generated-emails.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.generated-emails.restore', $this->id);
    }
}
