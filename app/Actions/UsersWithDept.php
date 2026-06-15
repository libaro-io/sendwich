<?php

namespace App\Actions;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UsersWithDept
{

    protected $company;
    protected $withOrdersForToday;
    protected $date;
    protected $users;
    protected $user;

    public function __construct()
    {
        $this->isToday = false;
        $this->withOrdersForToday = false;
        $this->date = null;
        $this->user = null;
    }

    public function execute()
    {
        $company = $this->getCompany();
        $query = User::query()
            ->where('company_id', '=', $company->id)
            ->where(function ($query) {
                $query->has('orders')
                    ->orHas('payments')
                    ->orHas('settlementsPaid')
                    ->orHas('settlementsReceived');
            })
            ->withSum(['payments AS payments_sum' => fn (Builder $query) => $this->excludePayouts($query)], 'total')
            ->withSum('settlementsPaid AS settlements_paid_sum', 'amount')
            ->when($this->withOrdersForToday, function ($q) {
                $q->whereHas('orders', function ($q2) {
                    $q2->where('date', '>=', Carbon::now()->startOfDay());
                    $q2->whereNull('paid_by');
                });
            })
            ->withSum(['orders AS orders_dept' => function (Builder $query) {
                $query->whereNotNull('paid_by');
                $this->excludePayouts($query);
                if ($this->date) {
                    $query->where('date', '>=', Carbon::now()->startOfDay());
                }
            }], 'total')
            ->withSum(['settlementsReceived AS settlements_received_sum' => function (Builder $query) {
                if ($this->date) {
                    $query->where('date', '>=', Carbon::now()->startOfDay());
                }
            }], 'amount');

        if ($this->user !== null) {
            $query->where('users.id', $this->user->id);
        }

        $this->users = $query->get();
        return $this->calculateDept();
    }

    public function calculateDept(): Collection
    {
        foreach ($this->users as $user) {
            $paid = ($user->payments_sum ?? 0) + ($user->settlements_paid_sum ?? 0);
            $owed = ($user->orders_dept ?? 0) + ($user->settlements_received_sum ?? 0);
            $user->dept = round($paid - $owed, 2);
        }

        $usersSorted = $this->users->sortBy('dept');
        return new Collection($usersSorted->values()->all());
    }

    private function excludePayouts(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query->whereNull('product_id')->orWhere('product_id', '!=', Order::PAYOUT_PRODUCT_ID);
        });
    }

    public function setWithOrdersForToday($withOrdersForToday): void
    {
        $this->withOrdersForToday = $withOrdersForToday;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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
}
