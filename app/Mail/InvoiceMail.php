<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function envelope(): Envelope
    {
        $property = $this->invoice->reservation?->property ?: Property::current();

        return new Envelope(
            subject: 'Invoice ' . $this->invoice->invoice_number . ' - ' . ($property->property_name_en ?? 'Hotel'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.voucher_invoice.email',
        );
    }
}
