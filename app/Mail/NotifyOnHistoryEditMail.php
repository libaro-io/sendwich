<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyOnHistoryEditMail extends Mailable
{
    use Queueable, SerializesModels;

    protected Order $order;
    protected string $oldProductName;
    protected string $newProductName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, string $oldProductName, string $newProductName)
    {
        $this->order = $order;
        $this->oldProductName = $oldProductName;
        $this->newProductName = $newProductName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.historyEditMail')->subject('Your order has been changed by ' . $this->order->deliverer->name)
            ->with([
                'order' => $this->order,
                'oldProductName' => $this->oldProductName,
                'newProductName' => $this->newProductName
            ]);
    }
}
