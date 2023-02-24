<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InformPaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    private User $receiver;
    private float $balance;
    private User $payer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $receiver, User $payer, float $balance)
    {
        $this->receiver = $receiver;
        $this->payer = $payer;
        $this->balance = $balance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.paymentReceivedMail')
            ->subject("Er is een Sendwich uitbetaling gebeurd")
            ->with([
                'receiverName' => $this->receiver->name,
                'payerName' => $this->payer->name,
                'balance' => $this->balance
            ]);
    }
}
