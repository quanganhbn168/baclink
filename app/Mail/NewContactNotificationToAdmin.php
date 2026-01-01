<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContactNotificationToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    // Biến public sẽ tự động được truyền sang view
    public Contact $contact;

    /**
     * Create a new message instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo: Có liên hệ mới từ Website',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Chỉ định file view cho nội dung email
            view: 'emails.admin-notification',
        );
    }
}