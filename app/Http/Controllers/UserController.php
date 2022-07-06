<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = $this->getUsersWithDept();

        return response()->json(['users' => $users]);
    }

    public  function getUsersWithDept()
    {
        $users = User::where(function ($query) {
            $query->has('orders')->orHas('payments');
        })->with(['orders' => function ($query) {
            $query->whereNotNull('paid_by');
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
