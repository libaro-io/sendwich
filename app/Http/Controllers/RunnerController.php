<?php

namespace App\Http\Controllers;

use App\Console\Commands\ChooseRandomVictim;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RunnerController extends Controller
{

    public function getSelectedRunner(Request $request)
    {
        if (Auth::check()) {
            $company = Auth::user()->company;
        } else {
            $company = Company::query()->where('token', $request->input('company_token'))->firstOrFail();
        }
        $order = Order::getOrders($company, $this->getDate())->first();
        if ($order) {
            $user = $order->deliverer;
        }
        return response()->json(['user' => $user ?? null]);
    }


}
