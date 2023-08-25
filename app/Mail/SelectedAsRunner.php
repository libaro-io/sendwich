<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SelectedAsRunner extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Collection<Order> $orders
     */
    public function __construct(
        private User $runner,
        private Collection $orders,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Libaro Sendwich needs you!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.selectedAsRunner',
            with: [
                'runner' => $this->runner,
                'orders' => $this->orders,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
