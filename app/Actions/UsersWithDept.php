<?php

namespace App\Actions;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UsersWithDept
{

    protected $company;
    protected $withOrdersForToday;
    protected $date;
    protected $users;

    public function __construct()
    {
        $this->isToday = false;
        $this->withOrdersForToday = false;
        $this->date = null;
    }

    public function execute()
    {
        $company = $this->getCompany();
        $this->users = User::query()->where('company_id', '=', $company->id)->where(function ($query) {
            $query->has('orders')->orHas('payments');
        })->with(['orders' => function ($query) {
            $query->whereNotNull('paid_by');
            if ($this->date) {
                $query->where('date', '>=', Carbon::now()->startOfDay());
            }
        }, 'payments'])
            ->when($this->withOrdersForToday, function ($q) {
                $q->whereHas('orders', function ($q2) {
                    $q2->where('date', '>=', Carbon::now()->startOfDay());
                });
            })->get();

        return $this->calculateDept();
    }

    public function calculateDept()
    {
        foreach ($this->users as $user) {
            $user->orders_dept = $user->orders->sum('total');
            $user->payments_sum = $user->payments->sum('total');
            $user->dept = round($user->payments_sum - $user->orders_dept, 2);
        }

        $usersSorted = $this->users->sortBy('dept');
        return new Collection($usersSorted->values()->all());
    }

    public function setWithOrdersForToday($withOrdersForToday): void
    {
        $this->withOrdersForToday = $withOrdersForToday;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function getCompany()
    {
        if ($this->company) {
            return $this->company;
        } elseif (auth()->check()) {
            return auth()->user()->company;
        } else {
            return null;
        }
    }

    public static function calculateUserDept(User $user):float
    {
        return round( $user->orders->sum('total') - $user->payments->sum('total'),2);
    }
}
