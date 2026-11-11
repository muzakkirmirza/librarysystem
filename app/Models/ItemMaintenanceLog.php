<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemMaintenanceLog extends Model
{
    protected $fillable = [
        'inventory_item_id', 
        'issue_description', 
        'status'
    ];
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
