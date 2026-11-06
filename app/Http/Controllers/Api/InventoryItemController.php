<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    // 1. Saare Inventory Items mangwana (Category details ke sath) (GET)
    public function index()
    {
        $items = InventoryItem::with('category')->get();
        return response()->json($items, 200);
    }

    // 2. Naya Item add karna (POST)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:item_categories,id',
            'name' => 'required|string|max:255',
            'condition' => 'required|string|in:Good,Fair,Needs Repair',
            'is_available' => 'boolean',
        ]);

        $item = InventoryItem::create($validated);
        return response()->json($item->load('category'), 201);
    }

    // 3. Specific Item ki detail dekhna (GET)
    public function show(string $id)
    {
        $item = InventoryItem::with(['category', 'maintenanceLogs'])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Inventory item not found'], 404);
        }

        return response()->json($item, 200);
    }

    // 4. Item update karna (PUT/PATCH)
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
        return response()->json($item->load('category'), 200);
    }

    // 5. Item delete karna (DELETE)
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