<?php

namespace App\Actions;

use App\Models\Product;

final class AddCustomItemToCatalogue
{
    /**
     * Add a custom item as a product to a store's catalogue, unless a
     * product with the same name already exists in that store.
     */
    public function execute(int $companyId, int $storeId, string $label, float $price): void
    {
        $name = trim($label);

        $exists = Product::query()
            ->where('company_id', '=', $companyId)
            ->where('store_id', '=', $storeId)
            ->where('name', '=', $name)
            ->exists();

        if ($exists) {
            return;
        }

        Product::query()->create([
            'name'           => $name,
            'description'    => null,
            'price'          => $price,
            'variable_price' => false,
            'company_id'     => $companyId,
            'store_id'       => $storeId,
        ]);
    }
}