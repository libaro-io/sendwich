<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\RemoveRequest;
use Illuminate\Http\RedirectResponse;

class RemoveProductController extends Controller
{
    public function __invoke(RemoveRequest $request): RedirectResponse
    {
        $order = $request->getOrder();

        abort_if($order === null, 404);

        $order->delete();

        return redirect()->back()->with(['success' => 'Your order has been deleted']);
    }
}