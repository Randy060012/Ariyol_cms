<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AFRIYOL - Nouveau contact : ' . ucfirst($this->contact->subject),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact', // Utiliser 'view' à la place de 'markdown'
            with: [
                'contact' => $this->contact,
            ],
        );
    }
}
