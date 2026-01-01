<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    // Các biến public sẽ tự động được truyền sang view
    public string $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chào mừng bạn đã đến với ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-welcome', // Chỉ định file view cho nội dung mail
        );
    }
}