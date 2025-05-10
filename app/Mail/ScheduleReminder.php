<?php

namespace App\Mail;

date_default_timezone_set('America/Sao_Paulo');

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduleReminder extends Mailable {
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $schedule;
    public $endereco;

    public function __construct($schedule, $endereco) {
        $this->schedule = $schedule;
        $this->endereco = $endereco;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Schedule',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content {
        return new Content(
            view: 'mails.reminder',
            with: [
                'schedule' => $this->schedule,
                'endereco' => $this->endereco,
            ]
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
