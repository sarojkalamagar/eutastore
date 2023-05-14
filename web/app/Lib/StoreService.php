<?php

namespace App\Lib;

use App\Models\Session;
use App\Models\Shop;
use Illuminate\Support\Facades\Cookie;
use Shopify\Utils;

class StoreService
{
    /**
     *--------------------------------------------------------------------------
     * Get session
     *--------------------------------------------------------------------------
     *
     * @param 
     * @return Session
     */

    public static function getSession($shop = null): Session
    {
        if (!$shop) {
            $shop = request()->get('shop');
        }

        if (!$shop) {
            $referer = request()->headers->get('referer');
            $refererArray = explode('&shop=', $referer);
            $refererArray = explode('&', $refererArray[1]);
            $shop = $refererArray[0];
        }

        if (!$shop) {
            $shop = Cookie::get('shop');
        }

        if (!$shop) {
            $shop = Utils::sanitizeShopDomain(request()->query('shop'));
        }

        return Session::query()->where('shop', $shop)->first();
    }

    /**
     *--------------------------------------------------------------------------
     * Get shop
     *--------------------------------------------------------------------------
     *
     * @param 
     * @return string
     */

    public static function getShopName(): string
    {
        $session = self::getSession();
        return $session->shop;
    }

    /**
     *--------------------------------------------------------------------------
     * Get access token
     *--------------------------------------------------------------------------
     *
     * @param 
     * @return string
     */

    public static function getAccessToken($shopName = null): string
    {
        $session = self::getSession($shopName);
        return $session->access_token;
    }

    /**
     *--------------------------------------------------------------------------
     * Get current shop
     *--------------------------------------------------------------------------
     *
     * @param 
     * @return Shop
     */

    public static function currentShop(): Shop
    {
        $shopName = self::getShopName();
        return Shop::query()->where('name', $shopName)->first();
    }
}
