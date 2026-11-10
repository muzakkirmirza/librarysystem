<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemMaintenanceLog;
use App\Http\Resources\ItemMaintenanceLogResource;
use Illuminate\Http\Request;

class ItemMaintenanceLogController extends Controller
{
    public function index()
    {
        return ItemMaintenanceLogResource::collection(ItemMaintenanceLog::with('item')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'issue_description' => 'required|string',
            'status' => 'required|string|in:under_repair,repaired,scrapped',
        ]);

        $log = ItemMaintenanceLog::create($validated);
        return new ItemMaintenanceLogResource($log->load('item'));
    }

    public function show(string $id)
    {
        $log = ItemMaintenanceLog::with('item')->find($id);

        if (!$log) {
            return response()->json(['message' => 'Maintenance log not found'], 404);
        }

        return new ItemMaintenanceLogResource($log);
    }

    public function update(Request $request, string $id)
    {
        $log = ItemMaintenanceLog::find($id);

        if (!$log) {
            return response()->json(['message' => 'Maintenance log not found'], 404);
        }

        $validated = $request->validate([
            'inventory_item_id' => 'sometimes|required|exists:inventory_items,id',
            'issue_description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:under_repair,repaired,scrapped',
        ]);

        $log->update($validated);
        return new ItemMaintenanceLogResource($log->load('item'));
    }

    public function destroy(string $id)
    {
        $log = ItemMaintenanceLog::find($id);

        if (!$log) {
            return response()->json(['message' => 'Maintenance log not found'], 404);
        }

        $log->delete();
        return response()->json(['message' => 'Maintenance log deleted successfully'], 200);
    }
}