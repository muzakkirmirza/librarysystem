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
    
    public function category()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    
    public function maintenanceLogs()
    {
        return $this->hasMany(ItemMaintenanceLog::class, 'inventory_item_id');
    }
}
