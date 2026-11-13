<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_request_id',
        'user_id',
        'inventory_item_id',
        'issue_date',
        'due_date',
        'return_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function request()
    {
        return $this->belongsTo(BorrowRequest::class, 'borrow_request_id');
    }
}