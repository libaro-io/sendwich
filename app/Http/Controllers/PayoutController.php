<?php

namespace App\Http\Controllers;

use App\Actions\UsersWithDept;
use App\Mail\InformPaymentPaidMail;
use App\Mail\InformPaymentReceivedMail;
use App\Mail\InformVictimMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
                $this->notifyUsers($paid_by, $user_id, $payout['paysBack']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your balance has been restored',
        ]);
    }

    private function notifyUsers(int $paid_by, int $user_id, float $balance)
    {
        $receiver = User::find($user_id);
        $payer = User::find($paid_by);

        Mail::to($receiver->email)->cc($payer->email)->send(new InformPaymentReceivedMail($receiver, $payer, $balance));
//        Mail::to($payer->email)->send(new InformPaymentPaidMail($receiver, $payer, $balance));
    }
}
