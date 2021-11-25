<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemHistory extends Model
{
    use HasFactory;

    protected $casts = [
        'value' => 'json'
    ];

    protected $fillable = [
        'item_id',
        'user_id',
        'timestamp',
        'value',
    ];


    /**
     * Item that this history record belongs to
     *
     * @return BelongsTo
     */
    public function object()
    {
        return $this->belongsTo(Item::class);
    }
}
