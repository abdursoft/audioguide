<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;
    

    // public variables for the mail class 
    public $title;
    public $description;
    public $reason;
    public $quantity;
    public $shop;
    public $email;
    public $phone;
    public $address;

    /**
     * Create a new message instance.
     */
    public function __construct($title,$description,$reason,$quantity=null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->reason = $reason;
        $this->quantity = $quantity;

        $this->shop = env('SHOP_NAME');
        $this->email = env('SHOP_EMAIL');
        $this->phone = env('SHOP_PHONE');
        $this->address = env('SHOP_ADDRESS');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'components.mail.notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
