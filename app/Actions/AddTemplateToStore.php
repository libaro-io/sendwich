<?php


namespace App\Actions;

use App\Models\Company;
use App\Models\Product;
use App\Models\Store;
use SebastianBergmann\Template\Template;

final class AddTemplateToStore
{
    private Store $store;
    private array $products;
    private Company $company;


    /**
     * @param Store $store
     * @param Template $template
     */
    public function __construct(Store $store, Company $company, array $products)
    {
        $this->store = $store;
        $this->products = $products;
        $this->company = $company;
    }

    public function execute()
    {
        foreach ($this->products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'company_id' => $this->company->id,
                'store_id' => $this->store->id
            ]);
        }

    }
}
