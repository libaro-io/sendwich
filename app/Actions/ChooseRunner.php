<?php


namespace App\Actions;

use App\Mail\SelectedAsRunner;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

final class ChooseRunner
{
    public function __construct(
        private Company $company,
        private ?User $user = null,
        private bool $addTomorrow = false
    ) {
    }

    public function execute(): ?User
    {
        if ($this->user != null) {
            $victim = $this->user;
        } else {
            $victim = $this->getVictim();
        }

        $orders = Order::getOrders($this->company, Carbon::now(), $this->addTomorrow)->get();
        if ($orders->count() > 0) {
            $this->setOrdersAppointed($orders, $victim);
            $this->sendMission($victim, $orders);
        }
        return $victim;
    }

    public function getSimulatedRunner()
    {
        return $this->getVictim();
    }

    private static function setOrdersAppointed($orders, $user)
    {
        foreach ($orders as $order) {
            $order->paid_by = $user->id;
            $order->save();
        }
    }

    private function getVictim()
    {
        $deptAction = new UsersWithDept();
        $deptAction->setCompany($this->company);
        $deptAction->setWithOrdersForToday(true);
        $users = $deptAction->execute();

        return $users->first();
    }


    private function sendMission($victim, $orders)
    {
        Mail::to($victim->email)->send(new SelectedAsRunner($victim, $orders));
    }
}
