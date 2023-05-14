<?php

declare(strict_types=1);

namespace App\Lib\Handlers;

use App\Models\Collect;
use App\Models\CustomCollection;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Shopify\Webhooks\Handler;
use App\Models\Session;
use App\Models\Shop;
use App\Models\Subscription;

class AppUninstalled implements Handler
{
    public function handle(string $topic, string $shop, array $body): void
    {
        Log::debug("App was uninstalled from $shop - removing all sessions");
        Session::where('shop', $shop)->delete();
        $shopModel = Shop::query()->where('name', $shop)->first();
        Shop::query()->where('name', $shop)->delete();
        if ($shop) {
            Collect::query()->where('shop_id', $shopModel->id)->delete();
            Product::query()->where('shop_id', $shopModel->id)->delete();
            CustomCollection::query()->where('shop_id', $shopModel->id)->delete();
            Subscription::query()->where('shop_id', $shopModel->id)->delete();
        }
    }
}
