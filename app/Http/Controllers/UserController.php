<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function getUsers()
    {
        $users = $this->getUsersWithDept();

        return response()->json(['users' => $users]);
    }

    public function getUsersWithDept($today = false)
    {
        $users = User::query()->where('company_id', $this->company->id)->where(function ($query) {
            $query->has('orders')->orHas('payments');
        })->with(['orders' => function ($query) use ($today) {
            $query->whereNotNull('paid_by');
            if ($today) {
                $query->where('date', '>=', Carbon::now()->startOfDay());
            }
        }, 'payments'])->get();
        $users = self::calculateDept($users);

        return $users;
    }

    public function calculateDept($users)
    {
        foreach ($users as $user) {
            $user->orders_dept = $user->orders->sum('total');
            $user->payments_sum = $user->payments->sum('total');
            $user->dept = round($user->payments_sum - $user->orders_dept, 2);
        }
        $users->sortByDesc('dept');
        return $users;
    }
}
