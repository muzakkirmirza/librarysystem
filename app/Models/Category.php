<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'item_categories'; // Yeh line table ka naam batayegi

    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }
}