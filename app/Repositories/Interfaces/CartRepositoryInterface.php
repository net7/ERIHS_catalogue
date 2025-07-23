<?php

namespace App\Repositories\Interfaces;

interface CartRepositoryInterface
{
    // private static \App\Models\Cart $cart;

    public static function getCartName();

    public static function getCart();

    public static function loadFromDb();

    public static function saveToDb();

    public static function hasItem($tool);

    public static function addItem($tool);

    public static function getItems();

}
