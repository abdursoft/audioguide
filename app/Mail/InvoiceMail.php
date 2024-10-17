<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable {
    use Queueable, SerializesModels;

    public $carts, $customer, $street, $address, $invoice, $vat, $total;

    /**
     * Create a new message instance.
     */
    public function __construct( $carts, $customer, $street, $address, $invoice, $vat, $total ) {
        $this->carts    = $carts;
        $this->customer = $customer;
        $this->street   = $street;
        $this->address  = $address;
        $this->invoice  = $invoice;
        $this->vat      = $vat;
        $this->total    = $total;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Invoice Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content {
        return new Content(
            view: 'components.mail.invoice',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array {
        return [];
    }
}
