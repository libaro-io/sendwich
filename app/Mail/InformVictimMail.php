<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InformVictimMail extends Mailable
{
    use Queueable, SerializesModels;


    public $orders;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($orders, $user)
    {
        $this->orders = $orders;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.deliveryMail')->subject('Libaro Sendwich needs you')
            ->with([
                    'orders' => $this->orders,
                    'user' => $this->user
                ]);
    }
}
