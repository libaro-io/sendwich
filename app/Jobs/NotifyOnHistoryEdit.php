<?php

namespace App\Jobs;

use App\Mail\InformVictimMail;
use App\Mail\NotifyOnHistoryEditMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyOnHistoryEdit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;
    protected string $oldProductName;
    private string $newProductName;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->order->user->email)->send(new NotifyOnHistoryEditMail($this->order, $this->oldProductName, $this->newProductName));
    }
}
