<?php

namespace App\Http\Controllers;

use App\Actions\AddTemplateToStore;
use App\Templates\FriesTemplate;
use App\Templates\PastaTemplate;
use App\Templates\SandwichTemplate;
use App\Http\Requests\CreateStore;
use App\Http\Requests\UpdateProduct;
use App\Models\Product;
use App\Models\Store;
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

        return Inertia::render('Store/Stores',
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

        return Inertia::render('Store/Products',
            [
                'store' => $company->stores()->where('id', $store_id)->with('products')->first(),
            ]);
    }


    /**
     * @param UpdateProduct $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProduct $request, Product $product)
    {
        $data = $request->input('product');
        $product->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
        ]);

        return response()->json([
            'message' => 'Product updated',
        ]);
    }

    public function store(UpdateProduct $request)
    {
        $company = auth()->user()->company;
        $data = $request->input('product');
        $store = Store::find($request->input('store_id'));
        Product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'company_id' => $company->id,
            'store_id' => $store->id
        ]);

        return response()->json([
            'message' => 'Product saved',
            'products' => $store->products
        ]);
    }

    public function addStore(CreateStore $request)
    {
        $storeData = $request->get('store');
        $storeData['company_id'] = auth()->user()->company_id;
        $store = Store::create($storeData);

        if ($storeData['template'] !== null) {
            $products = [];
            if ($storeData['template'] === SandwichTemplate::$name) {
                $products = SandwichTemplate::$products;
            } else if ($storeData['template'] === PastaTemplate::$name) {
                $products = PastaTemplate::$products;
            } else if ($storeData['template'] === FriesTemplate::$name) {
                $products = FriesTemplate::$products;
            }

            $templateAction = new AddTemplateToStore($store, auth()->user()->company, $products);
            $templateAction->execute();
        }
        return $this->show($store->id);
    }

    public function delete(Product $product)
    {
        $store = $product->store;
        $product->delete();

        return response()->json([
            'message' => 'Product deleted',
            'products' => $store->products
        ]);
    }
}
