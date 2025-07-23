<?php


namespace App\Services;

use App\Models\Cart;
use App\Services\ERIHSCartServiceInterface;
use Illuminate\Support\Facades\Auth;

class ERIHSCartService extends  ERIHSCommonDBCartService
{
    public static function getCartName(): string
    {
        return 'cart';
    }

    public static function addItem($itemId){
        if (ERIHSAuthService::checkLogged()) {
            parent::addItem($itemId);
        } else {
            ERIHSLocalCartService::addItem($itemId);
        }
    }

    public static function removeItem($itemId)
    {
        if (ERIHSAuthService::checkLogged()) {
            parent::removeItem($itemId);
        } else {
            ERIHSLocalCartService::removeItem($itemId);
        }
    }

    public static function hasItem($itemId)
    {
        if (ERIHSAuthService::checkLogged()) {
            return parent::hasItem($itemId);
        } else {
            return ERIHSLocalCartService::hasItem($itemId);
        }
    }

    public static function getItemsCount()
    {
        if (ERIHSAuthService::checkLogged()) {
            return parent::getItemsCount();
        } else {
            return ERIHSLocalCartService::getItemsCount();
        }
    }

    public static function getItems(){
        if (ERIHSAuthService::checkLogged()) {
            return parent::getItems();
        } else {
            return ERIHSLocalCartService::getItems();
        }
    }

    public static function getItemsIds(){
        if (ERIHSAuthService::checkLogged()) {
            return parent::getItemsIds();
        } else {
            return ERIHSLocalCartService::getItemsIds();
        }
    }

    public static function emptyCart()
    {
        resolve(ERIHSLocalCartService::class)::emptyCart();
        // ERIHSLocalCartService::emptyCart();
        $cart = Cart::where('cart_name','=', static::getCartName())->where('user_id', '=',Auth::user()->id)->first();
        if ($cart){
            $cart->data = json_encode([]);
            $cart->update();
        }
    }
}
