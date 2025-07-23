<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Database\Eloquent\Model;

class ERIHSCommonDBCartService implements ERIHSCartServiceInterface
{
    private static \App\Models\Cart $cart;

    public static function getCartName()
    {
        return "To be implemented in child classes";
    }

    public static function getCart()
    {
        return \App\Models\Cart::firstOrNew(['cart_name' => static::getCartName(), 'user_id' => Auth::user()->id]);
    }

    public static function addItem($serviceId)
    {
            $cart = self::getCart();
            $servicesIds = json_decode($cart->data, true) ?? [];
            if (! in_array($serviceId, $servicesIds)) {
                $servicesIds[] = $serviceId;
            }
            $cart->data = json_encode($servicesIds);
            $cart->save();
    }

    public static function removeItem($serviceId)
    {
            $cart = self::getCart();
            $servicesIds = $cart->data ?  json_decode($cart->data, true) : [];
            if (in_array($serviceId, $servicesIds)) {
                $servicesIds = array_diff($servicesIds, [$serviceId]);
            }
            $cart->data = json_encode($servicesIds);
            $cart->save();
    }

    public static function hasItem($serviceId)
    {
            $cart = self::getCart();
            $services = $cart->data ? json_decode($cart->data, true) : [];
            if (in_array($serviceId, $services)) {
                return true;
            }

            return false;
    }

    public static function getItemsCount()
    {
        $cart = self::getCart();
        $servicesIds = $cart->data ? json_decode($cart->data, true) : [];

        return count($servicesIds);
    }

    public static function getItems()
    {
        $services = new Collection();
        foreach (self::getItemsIds() as $serviceId) {
            $service = Service::query()->where('id', '=', $serviceId)->first();
            $services->add($service);
        }

        return $services;
    }

    public static function getItemsIds(){
        $cart = self::getCart();
        return json_decode($cart->data, true) ?? [];
    }

}
