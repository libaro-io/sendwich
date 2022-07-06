<?php

namespace App\Console\Commands;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Mail\InformVictim;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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


    public $oc;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->oc = new OrderController();
        $victim = $this->getVictim();
        if ($victim->name === 'Jennis Vanhaeke') {
            $victim = $this->pickAnotherVictim($victim);
        }
        $orders = $this->getOrders();
        $this->oc->setOrdersAppointed($orders, $victim);

        $this->sendMission($victim, $orders);
    }

    private function sendMission($victim, $orders)
    {
        Mail::to($victim->email)->bcc('jennis@libaro.be')->send(new InformVictim($orders, $victim));
    }

    private function getOrders()
    {
        return Order::getOrders($this->oc->getDate())->get();
    }

    private function getVictim()
    {
        $users = (new UserController())->getUsersWithDept(true);

        foreach ($users as $user) {
            $user->deptFactor = ($user->depth * -1) * $this->getRandomNumber();
        }

        $users->sortByDesc('deptFactor');
        return $users->first();
    }

    private function pickAnotherVictim($victim)
    {
        return $victim; // your lack of faith in me is disappointing
    }

    private function getRandomNumber()
    {
        return 1 + (rand(1, 99) / 100);
    }
}
