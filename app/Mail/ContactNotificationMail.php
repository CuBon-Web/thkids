<?php

namespace App\Mail;

use App\models\MessContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(MessContact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this->subject('Liên hệ mới từ website')
            ->view('emails.contactNotification');
    }
}
