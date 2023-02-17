<?php

namespace App\Http\Controllers;

use App\Actions\UsersWithDept;
use App\Models\Order;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function payout(Request $request)
    {
        $payouts = $request->get('payouts');

        $user = auth()->user();
        $deptAction = new UsersWithDept();
        $deptAction->setUser($user);
        $usersWithBalance = $deptAction->execute();

        if ($usersWithBalance->first()) {
            $userWithBalance = $usersWithBalance->first();


            foreach ($payouts as $payout) {
                if ($userWithBalance->dept > 0) {
                    $user_id = $userWithBalance->id;
                    $paid_by = $payout['id'];
                } else {
                    $user_id = $payout['id'];
                    $paid_by = $userWithBalance->id;
                }
                Order::query()->create([
                    'user_id' => $user_id,
                    'paid_by' => $paid_by,
                    'company_id' => $user->company->id,
                    'product_id' => 65,
                    'quantity' => 1,
                    'total' => $payout['paysBack'],
                    'date' => now()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => '',
        ]);


    }
}
