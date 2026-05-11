<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentExpiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageText;

    public $document;

    public $entity;

    public function __construct($messageText, $document, $entity)
    {
        $this->messageText = $messageText;
        $this->document = $document;
        $this->entity = $entity;
    }

    // Set email subject
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Document Expiry Notification'
        );
    }

    // Specify the email content (Blade view)
    public function content(): Content
    {
        return new Content(
            view: 'Admin.Backend.emails.document_expiry'
        );
    }
}
