<?php

namespace App\Lib;

use App\Models\Product;
use App\Models\Shop;

class ProductService
{
    /**
     *--------------------------------------------------------------------------
     * Fetch products from shopify store
     *--------------------------------------------------------------------------
     *
     */

    public static function fetchProducts($shopName = null)
    {
        /*
        |--------------------------------------------------------------------------
        | Fetch total number of products
        |--------------------------------------------------------------------------
        |
        */

        $path = "admin/api/2023-04/products/count.json";
        $response = ApiService::getRequest($path);
        $numberOfProducts = $response['count'];

        /*
        |--------------------------------------------------------------------------
        | Derive number of times required to hit products API based on total
        | number of products because Shopify allows to fetch max 250 products
        | at once.
        |--------------------------------------------------------------------------
        |
        */

        if ($numberOfProducts % 250) $apiHitsCount = $numberOfProducts / 250;
        else $apiHitsCount = (int)($numberOfProducts / 250) + 1;

        /*
        |--------------------------------------------------------------------------
        | Shop details
        |--------------------------------------------------------------------------
        |
        */

        if (!$shopName) {
            $shopName = StoreService::getShopName();
        }
        $shop = Shop::query()->firstOrCreate([
            'name' => $shopName
        ]);

        /*
        |--------------------------------------------------------------------------
        | Fetch products
        |--------------------------------------------------------------------------
        |
        */

        $path = "admin/api/2023-04/products.json";
        $queryParams = ['limit' => 250];

        for ($i = $apiHitsCount; $i >= 1; $i--) {
            $response = ApiService::getRequest($path, $queryParams);
            $products = $response['products'];
            foreach ($products as $product) {
                Product::query()->create([
                    'shop_id' => $shop->id,
                    'body_html' => $product['body_html'],
                    'created_at' => $product['created_at'],
                    'handle' => $product['handle'],
                    'id' => $product['id'],
                    'product_type' => $product['product_type'],
                    'published_at' => $product['published_at'],
                    'published_scope' => $product['published_scope'],
                    'status' => $product['status'],
                    'tags' => $product['tags'],
                    'template_suffix' => $product['template_suffix'],
                    'title' => $product['title'],
                    'updated_at' => $product['updated_at'],
                    'vendor' => $product['vendor'],
                ]);
            }
        }
    }
}
