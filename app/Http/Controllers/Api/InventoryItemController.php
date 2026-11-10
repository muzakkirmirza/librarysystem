<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Http\Resources\InventoryItemResource;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::with('category');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_available')) {
            $query->where('is_available', filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN));
        }

        return InventoryItemResource::collection($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:item_categories,id',
            'name' => 'required|string|max:255',
            'condition' => 'required|string|in:Good,Fair,Needs Repair',
            'is_available' => 'boolean',
        ]);

        $item = InventoryItem::create($validated);
        return new InventoryItemResource($item->load('category'));
    }

    public function show(string $id)
    {
        $item = InventoryItem::with(['category', 'maintenanceLogs'])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Inventory item not found'], 404);
        }

        return new InventoryItemResource($item);
    }

    public function update(Request $request, string $id)
    {
        $item = InventoryItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Inventory item not found'], 404);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:item_categories,id',
            'name' => 'sometimes|required|string|max:255',
            'condition' => 'sometimes|required|string|in:Good,Fair,Needs Repair',
            'is_available' => 'boolean',
        ]);

        $item->update($validated);
        return new InventoryItemResource($item->load('category'));
    }

    public function destroy(string $id)
    {
        $item = InventoryItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Inventory item not found'], 404);
        }

        $item->delete();
        return response()->json(['message' => 'Inventory item deleted successfully'], 200);
    }
}