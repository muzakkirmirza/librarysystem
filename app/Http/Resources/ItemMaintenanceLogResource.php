<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemMaintenanceLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'issue_description' => $this->issue_description,
            'status' => $this->status,
            'item' => new InventoryItemResource($this->whenLoaded('item')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}