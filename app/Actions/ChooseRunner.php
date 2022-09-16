<?php


namespace App\Actions;

use App\Actions\ChooseRunner as ChooseRandomVictimAlias;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Mail\InformVictim;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

final class ChooseRunner
{
    private Company $company;
    private ?User $user;
    private $addTomorrow;

    /**
     * @param Company $company
     * @param $user
     * @param $addTomorrow
     */
    public function __construct(Company $company, ?User $user = null, bool $addTomorrow = false)
    {
        $this->setCompany($company);
        $this->setUser($user);
        $this->addTomorrow = $addTomorrow;
    }

    public function execute(): User
    {
        if($this->user != null){
            $victim = $this->user;
        }else{
            $victim = $this->getVictim();
        }
        $orders = Order::getOrders($this->company, Carbon::now(), $this->addTomorrow)->get();
        $this->setOrdersAppointed($orders, $victim);

        $this->sendMission($victim, $orders);
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

    private function getRandomNumber()
    {
        return 1 + (rand(1, 99) / 100);
    }


    /**
     * @param $company
     * @return $this
     */
    private function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @param $user
     * @return $this
     */
    private function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    private function sendMission($victim, $orders)
    {
        Mail::to($victim->email)->bcc('jennis@libaro.be')->send(new InformVictim($orders, $victim));
    }
}
