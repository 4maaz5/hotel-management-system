<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function envelope(): Envelope
    {
        $integrationName = $this->requestData['integration_name'] ?? 'Subscription';
        $propertyName = $this->requestData['property_name'] ?? 'Property';

        return new Envelope(
            subject: "Subscription Request - {$integrationName} - {$propertyName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_request',
        );
    }
}
