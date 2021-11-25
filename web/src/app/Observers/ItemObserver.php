<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\ItemHistory;
use App\Helpers\ItemRequestHelper;

class ItemObserver
{
    /**
     * Handle the Item "created" event.
     *
     * @param  \App\Models\Item  $item
     * @return void
     */
    public function saved(Item $item)
    {
        ItemHistory::create([
            'item_id' => $item->id,
            'value' => $item->value,
            'timestamp' => $item->timestamp,
            'user_id' => $item->user_id,
        ]);

        ItemRequestHelper::clearCache($item->key);
        ItemRequestHelper::clearCache($item->key, $item->timestamp);
        ItemRequestHelper::clearAllRecordsCache();
    }
}
