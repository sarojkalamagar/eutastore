<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\NewSubscriptionRequest;
use App\Lib\CollectService;
use App\Lib\CustomCollectionService;
use App\Lib\ProductService;
use App\Lib\StoreService;
use App\Models\Session;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Shopify\Clients\Graphql;

class SubscriptionController extends Controller
{

    /**
     *--------------------------------------------------------------------------
     * Subscribe
     *--------------------------------------------------------------------------
     *
     * @param App\Http\Requests\Subscription\NewSubscriptionRequest $request
     * @return Illuminate\Http\RedirectResponse
     */

    public function subscribe(NewSubscriptionRequest $request)
    {
        $shopName = $request->shop;
        $shop = Shop::query()->firstOrCreate([
            'name' => $shopName
        ]);
        Subscription::query()->create([
            'shop_id' => $shop->id,
            'subscription_plan_id' => $request->subscriptionPlanId
        ]);

        /*
        |--------------------------------------------------------------------------
        | Fetch access token
        |--------------------------------------------------------------------------
        |
        */

        $shop = StoreService::getShopName();
        $session = Session::query()->where('shop', $shop)->first();
        $accessToken = $session->access_token;

        /*
        |--------------------------------------------------------------------------
        | Graph query parameters
        |--------------------------------------------------------------------------
        |
        */

        $subscriptionPlan = SubscriptionPlan::query()->find($request->subscriptionPlanId);
        $name = $subscriptionPlan->name;
        $returnUrl = 'https://' . $shop;
        $amount = $subscriptionPlan->amount;
        $currency = strtoupper($subscriptionPlan->currency);
        switch ($subscriptionPlan->billing_interval) {
            case 'one-time':
                $interval = 'ONE_TIME';
                break;
            case 'every-30-days':
                $interval = 'EVERY_30_DAYS';
                break;
            case 'annual':
                $interval = 'ANNUAL';
                break;
        }

        $lineItems = [
            [
                "plan" => [
                    "appRecurringPricingDetails" => [
                        "price" => ["amount" => $amount, "currencyCode" => $currency],
                        "interval" => $interval
                    ]
                ]
            ]
        ];

        $variables = [
            "name" => $name,
            "returnUrl" => $returnUrl,
            "lineItems" => $lineItems,
            "test" => true
        ];

        /*
        |--------------------------------------------------------------------------
        | Graph query
        |--------------------------------------------------------------------------
        |
        */

        $client = new Graphql($shop, $accessToken);
        $query = <<<'QUERY'
        mutation createPaymentMutation(
            $name: String!
            $lineItems: [AppSubscriptionLineItemInput!]!
            $returnUrl: URL!
            $test: Boolean
        ) {
            appSubscriptionCreate(
                name: $name
                lineItems: $lineItems
                returnUrl: $returnUrl
                test: $test
            ) {
                confirmationUrl
                userErrors {
                    field, message
                }
            }
        }
        QUERY;

        $response = $client->query(["query" => $query, "variables" => $variables]);
        $responseBody = $response->getDecodedBody();

        ProductService::fetchProducts($shop);

        CustomCollectionService::fetchCustomCollections($shop);

        CollectService::fetchCollects($shop);

        return response()->json([
            'data' => [
                'confirmationUrl' => $responseBody['data']['appSubscriptionCreate']['confirmationUrl']
            ]
        ], 200);
    }
}
