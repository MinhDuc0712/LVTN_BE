<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $roomName, $startDate, $duration, $totalCost;
    /**
     * Create a new message instance.
     */
    public function __construct($roomName, $startDate, $duration, $totalCost)
    {
        $this->roomName = $roomName;
        $this->startDate = $startDate;
        $this->duration = $duration;
        $this->totalCost = $totalCost;
    }
    public function build()
    {
        return $this->subject("Xác nhận đặt phòng {$this->roomName}")
            ->view('emails.booking_confirmation');
    }
    /**
     * Get the message envelope.
     */


    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

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
