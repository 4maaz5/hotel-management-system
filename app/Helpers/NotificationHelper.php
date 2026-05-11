<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function create($userId, $type, $title, $titleAr, $message, $messageAr, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'title_ar' => $titleAr,
            'message' => $message,
            'message_ar' => $messageAr,
            'data' => $data,
        ]);
    }

    public static function notifyArrivals($userId, $count)
    {
        if ($count > 0) {
            self::create(
                $userId,
                'arrival',
                "$count arrivals expected today",
                "$count وصول متوقع اليوم",
                "You have $count reservation(s) checking in today",
                "لديك $count حجز (حجوزات) تسجل وصول اليوم"
            );
        }
    }

    public static function notifyDepartures($userId, $count)
    {
        if ($count > 0) {
            self::create(
                $userId,
                'departure',
                "$count departures today",
                "$count مغادرة اليوم",
                "You have $count guest(s) checking out today",
                "لديك $count نزيل (نزلاء) يغادرون اليوم"
            );
        }
    }

    public static function notifyNewReservation($userId, $reservation)
    {
        $guestName = $reservation->guest->first_name ?? 'Guest';
        $guestNameAr = $reservation->guest->first_name ?? 'ضيف';
        self::create(
            $userId,
            'new_reservation',
            'New reservation created',
            'تم إنشاء حجز جديد',
            "New reservation #{$reservation->reservation_number} from {$guestName}",
            "حجز جديد رقم {$reservation->reservation_number} من {$guestNameAr}"
        );
    }

    public static function notifyCheckIn($userId, $reservation)
    {
        $room = $reservation->unit->unit_number ?? 'N/A';
        $guestName = $reservation->guest->first_name ?? 'Guest';
        $guestNameAr = $reservation->guest->first_name ?? 'ضيف';
        self::create(
            $userId,
            'check_in',
            'Guest checked in',
            'تسجيل وصول النزيل',
            "{$guestName} checked in to Room $room",
            "تسجيل وصول {$guestNameAr} إلى الغرفة $room"
        );
    }

    public static function notifyCheckOut($userId, $reservation)
    {
        $room = $reservation->unit->unit_number ?? 'N/A';
        $guestName = $reservation->guest->first_name ?? 'Guest';
        $guestNameAr = $reservation->guest->first_name ?? 'ضيف';
        self::create(
            $userId,
            'check_out',
            'Guest checked out',
            'مغادرة النزيل',
            "{$guestName} checked out from Room $room",
            "مغادرة {$guestNameAr} من الغرفة $room"
        );
    }

    public static function notifyPayment($userId, $amount, $reservationNumber = null)
    {
        self::create(
            $userId,
            'payment',
            'Payment received',
            'تم استلام دفعة',
            'Payment of SAR '.number_format($amount, 2).' received'.($reservationNumber ? " for Reservation #$reservationNumber" : ''),
            'تم استلام دفعة قدرها '.number_format($amount, 2).' ريال'.($reservationNumber ? " للحجز رقم #$reservationNumber" : '')
        );
    }
}
