<?php


namespace App\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ERIHSLocalCartService implements ERIHSCartServiceInterface
{
    public static function getItemsIds()
    {
        return json_decode(request()->session()->get('cart'), true) ?? [];
    }

    public static function emptyCart(): void
    {
        request()->session()->put('cart', json_encode([]));
    }

    public static function transferToDbCart(): void
    {
        $itemIdsInCart = self::getItemsIds();
        foreach ($itemIdsInCart as $itemId){
            ERIHSCartService::addItem($itemId);
        }
        self::emptyCart();
    }

    public static function addItem($itemId): void
    {
        // Retrieve the current cart data from localStorage.
        $itemIdsInCart = self::getItemsIds();

        // Check if the item is already in the cart
        if (!in_array($itemId, $itemIdsInCart)) {
            $itemIdsInCart[] = $itemId;
        }
        // Store the updated cart in the session (not localStorage).
        request()->session()->put('cart', json_encode($itemIdsInCart));
    }
    public static function removeItem($itemId): void
    {
        // Retrieve the current cart data from localStorage.
        $itemIdsInCart = self::getItemsIds();

        // Check if the item is already in the cart
        if (in_array($itemId, $itemIdsInCart)) {
            $itemIdsInCart = array_diff($itemIdsInCart, [$itemId]);
            // Store the updated cart in the session (not localStorage).
            request()->session()->put('cart', json_encode($itemIdsInCart));
        }
    }

    public static function hasItem($itemId): bool
    {
        $itemIdsInCart = self::getItemsIds();

        // Check if the item is in the cart
        if (in_array($itemId, $itemIdsInCart)) {
            return true;
        }
        return false;
    }

    public static function getItemsCount(): int
    {
        return count(self::getItemsIds());
    }

    public static function getItems(): Collection
    {
        $itemsIds = self::getItemsIds();
        $items = new Collection();
        foreach ($itemsIds as $itemId) {
            $item = Service::query()->where('id', '=', $itemId)->first();
            $items->add($item);
        }

        return $items;
    }
}
