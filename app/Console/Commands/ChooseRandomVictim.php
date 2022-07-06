<?php

namespace App\Console\Commands;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

class ChooseRandomVictim extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChooseRandomVictim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searching an innocent victim and force him to get some sandwiches';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $victim = $this->getVictim();
        if ($victim->name === 'Jennis Vanhaeke') {
            $victim = $this->pickAnotherVictim($victim);
        }
        $orders = $this->getOrders();

        $this->sendMission($victim, $orders);
    }

    private function sendMission($victim, $orders)
    {

    }

    private function getOrders()
    {
        $oc = new OrderController();
        return Order::getOrders($oc->getDate())->get();
    }


    private function getVictim()
    {
        $users = User::has('orders')->with('orders', 'payments')->get();

        foreach ($users as $user) {
            $user->deptFactor = ($user->orders->sum('total') - $user->payments->sum('total')) * $this->getRandomNumber();
        }

        $users->sortByDesc('deptFactor');
        return $users->first();
    }

    private function pickAnotherVictim($victim)
    {
        // Have more faith
        return $victim;
    }

    private function getRandomNumber()
    {
        return 1 + (rand(1, 99) / 100);
    }
}
