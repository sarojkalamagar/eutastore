<?php

namespace App\Lib;

use App\Models\Collect;
use App\Models\Shop;

class CollectService
{
    /**
     *--------------------------------------------------------------------------
     * Fetch collects from shopify store
     *--------------------------------------------------------------------------
     *
     */

    public static function fetchCollects($shopName = null)
    {
        $path = "admin/api/2023-04/collects.json";
        $queryParams = ['limit' => 250];
        $response = ApiService::getRequest($path, $queryParams);
        $collects = $response['collects'];
        if (!$shopName) {
            $shopName = StoreService::getShopName();
        }
        $shop = Shop::query()->firstOrCreate([
            'name' => $shopName
        ]);
        foreach ($collects as $collect) {
            $collect['shop_id'] = $shop->id;
            Collect::query()->create($collect);
        }
    }
}
