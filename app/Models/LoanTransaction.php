<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanTransaction extends Model
{
    protected $fillable = [
        'borrow_request_id', 
        'user_id', 
        'inventory_item_id', 
        'issue_date', 
        'due_date', 
        'return_date', 
        'status'
    ];
}
