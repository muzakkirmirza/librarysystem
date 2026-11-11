<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    protected $fillable = [
        'user_id', 
        'inventory_item_id', 
        'status', 
        'rejection_reason'
    ];
}
