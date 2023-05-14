<?php

namespace App\Lib;

use App\Models\CustomCollection;
use App\Models\Shop;

class CustomCollectionService
{
    /**
     *--------------------------------------------------------------------------
     * Fetch custom collections from shopify store
     *--------------------------------------------------------------------------
     *
     */

    public static function fetchCustomCollections($shopName = null)
    {
        /*
        |--------------------------------------------------------------------------
        | Fetch total number of custom collections
        |--------------------------------------------------------------------------
        |
        */

        $path = "admin/api/2023-04/custom_collections/count.json";
        $response = ApiService::getRequest($path);
        $numberOfCustomCollections = $response['count'];

        logger($response);
        logger($response['count']);

        /*
        |--------------------------------------------------------------------------
        | Derive number of times required to hit custom collections API based on
        | total number of custom collections because Shopify allows to fetch 
        | max 250 custom collections at once.
        |--------------------------------------------------------------------------
        |
        */

        if ($numberOfCustomCollections % 250) $apiHitsCount = (int)($numberOfCustomCollections / 250);
        else $apiHitsCount = (int)($numberOfCustomCollections / 250) + 1;

        /*
        |--------------------------------------------------------------------------
        | Fetch custom collections
        |--------------------------------------------------------------------------
        |
        */

        $path = "admin/api/2023-04/custom_collections.json";
        $queryParams = ['limit' => 250];

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

        for ($i = $apiHitsCount; $i >= 1; $i--) {
            $response = ApiService::getRequest($path, $queryParams);
            $customCollectioons = $response['custom_collections'];
            foreach ($customCollectioons as $customCollection) {
                CustomCollection::query()->create([
                    'shop_id' => $shop->id,
                    'body_html' => $customCollection['body_html'],
                    'handle' => $customCollection['handle'],
                    'id' => $customCollection['id'],
                    'published' => $customCollection['published'],
                    'published_at' => $customCollection['published_at'],
                    'published_scope' => $customCollection['published_scope'],
                    'sort_order' => $customCollection['sort_order'],
                    'template_suffix' => $customCollection['template_suffix'],
                    'title' => $customCollection['title'],
                    'updated_at' => $customCollection['updated_at'],
                ]);
            }
        }
    }
}
