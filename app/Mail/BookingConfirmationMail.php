<?php

namespace App\Mail;

use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function envelope(): Envelope
    {
        $property = $this->reservation->property ?: Property::current();

        return new Envelope(
            subject: 'Booking Confirmation ' . $this->reservation->reservation_number . ' - ' . ($property->property_name_en ?? 'Hotel'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_confirmation',
        );
    }
}
