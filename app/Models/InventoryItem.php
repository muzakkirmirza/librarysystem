<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'category_id', 
        'name', 
        'condition', 
        'is_available'
    ];
}
