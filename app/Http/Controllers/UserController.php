<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::has('orders')->with('orders', 'payments')->get();
        $users = $this->calculateDept($users);

        return response()->json(['users' => $users]);
    }

    public function calculateDept($users)
    {
        foreach ($users as $user) {
            $user->orders_dept = $user->orders->sum('total');
            $user->payments_sum = $user->payments->sum('total');
            $user->dept = $user->payments_sum - $user->orders_dept;
        }
        $users->sortByDesc('dept');
        return $users;
    }
}
