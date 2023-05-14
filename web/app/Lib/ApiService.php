<?php

namespace App\Lib;

use GuzzleHttp\Client;

class ApiService
{
    /**
     *--------------------------------------------------------------------------
     * GET request
     *--------------------------------------------------------------------------
     *
     */

    public static function getRequest($path, $query = [])
    {
        $shop = StoreService::getShopName();
        $accessToken = StoreService::getAccessToken();

        $endpoint = "https://$shop/$path";

        $client = new Client();
        $response = $client->request('GET', $endpoint, ['query' => $query, 'headers' => ['X-Shopify-Access-Token' => $accessToken]]);
        $content = $response->getBody();
        return json_decode($content, true);
    }
}
