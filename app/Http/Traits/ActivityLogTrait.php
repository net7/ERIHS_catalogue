<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;

trait ActivityLogTrait
{

    protected function getItemLogString($item, $type)
    {
        if (method_exists($item, 'getLogString')) {
            return $item->getLogString($type);
        }


        $itemString = Str::afterLast(get_class($item), "\\");
        $itemString = Str::replace("_", " ", Str::snake($itemString));

        $itemString = Str::studly($type) . " " . $itemString . ' - Id: ' . $item->getKey();

        return $itemString;
    }

    protected function getItemLogProperties($item, $type, $oldItem = null)
    {

        if (method_exists($item, 'getLogProperties')) {
            return $item->getLogProperties($type, $oldItem);
        }

        $properties = [
            'attributes' => $item->getAttributes(),
        ];
        if ($type == 'update') {
            $properties['old'] = $oldItem ? $oldItem->getOriginal() : $item->getOriginal();
        }
        return $properties;
    }
}
