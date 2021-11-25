<?php

namespace App\Models;

use App\Models\ItemHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $casts = [
        'value' => 'json'
    ];

    protected $fillable = [
        'key',
        'timestamp',
        'value',
        'user_id',
    ];

    /**
     * Histories record of the object
     *
     * @return HasMany
     */
    public function histories()
    {
        return $this->hasMany(ItemHistory::class);
    }
}
