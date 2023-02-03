<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyOnHistoryEdit;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function showHistory()
    {
        $user = auth()->user();
        $company = $user->company;

        $products = $company->getProducts();

        return Inertia::render('History',
            [
                'company' => $company,
                'products' => $products
            ]);
    }

    public function updateOrder(Request $request)
    {
        $orders = $request->get('data');
        foreach ($orders as $orderGroup) {
            foreach ($orderGroup as $newOrder) {
                $order = Order::find($newOrder['id']);
                if ($newOrder['product']['id'] !== $order->product_id) {
                    $oldProduct = $order->product;
                    $newProduct = Product::find($newOrder['product']['id']);
                    $newPrice = $newProduct->price * $newOrder['quantity'];
                    $order->update([
                        'product_id' => $newProduct->id,
                        'total' => $newPrice
                    ]);
                    NotifyOnHistoryEdit::dispatch($order, $oldProduct->name, $newProduct->name);
                }
            }
        }

        return response()->json();
    }
}
