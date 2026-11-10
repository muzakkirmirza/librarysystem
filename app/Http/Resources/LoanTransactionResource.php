<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'return_date' => $this->return_date,
            'status' => $this->status,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'item' => new InventoryItemResource($this->whenLoaded('item')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}