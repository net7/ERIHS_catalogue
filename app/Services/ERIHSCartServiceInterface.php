<?php

namespace App\Services;

interface ERIHSCartServiceInterface{

    public static function addItem($toolId);
    public static function removeItem($toolId);
    public static function getItemsCount();
    public static function getItems();
    public static function hasItem($toolId);
    public static function getItemsIds();

}
