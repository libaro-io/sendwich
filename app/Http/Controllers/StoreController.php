<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        return Inertia::render('Store/Index',
            [
                'stores' => $company->stores,
                'company' => $company,
            ]);
    }

    /**
     * @param int $store_id
     * @return \Inertia\Response
     */
    public function show(int $store_id)
    {
        $user = auth()->user();
        $company = $user->company;

        return Inertia::render('Store/Detail',
            [
                'store' => $company->stores()->where('id', $store_id)->with('products')->first(),
            ]);
    }

    public function update(UpdateProduct $request, Product $product)
    {
        $user = auth()->user();
        $company = $user->company;
        $product = $company->products()->where('id', $request->input('id'))->firstOrFail();

        $product->update($request->all());


    }
}
