<?php

namespace App\Http\Controllers\Store;

use App\Actions\AddTemplateToStore;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStore;
use App\Models\Store;
use App\Templates\FriesTemplate;
use App\Templates\PastaTemplate;
use App\Templates\SandwichTemplate;
use Illuminate\Http\JsonResponse;

class AddStoreController extends Controller
{
    public function __invoke(CreateStore $request): JsonResponse
    {
        $storeData = $request->get('store');
        $storeData['company_id'] = auth()->user()->company_id;

        $store = Store::create($storeData);

        if ($storeData['template'] !== null) {
            $products = match ($storeData['template']) {
                SandwichTemplate::$name => SandwichTemplate::$products,
                PastaTemplate::$name    => PastaTemplate::$products,
                FriesTemplate::$name    => FriesTemplate::$products,
                default                 => [],
            };

            if (!empty($products)) {
                new AddTemplateToStore($store, auth()->user()->company, $products)->execute();
            }
        }

        return response()->json([
            'message' => 'Store added',
            'store'   => $store,
        ]);
    }
}
