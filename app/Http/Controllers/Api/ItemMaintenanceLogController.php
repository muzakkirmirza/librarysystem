<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemMaintenanceLog;
use Illuminate\Http\Request;

class ItemMaintenanceLogController extends Controller
{
    // 1. Saare Maintenance Logs dekhna (Item ki detail ke sath) (GET)
    public function index()
    {
        $logs = ItemMaintenanceLog::with('item')->get();
        return response()->json($logs, 200);
    }

    // 2. Naya Maintenance Record add karna (POST)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'issue_description' => 'required|string',
            'status' => 'required|string|in:under_repair,repaired,scrapped',
        ]);

        $log = ItemMaintenanceLog::create($validated);
        return response()->json($log->load('item'), 201);
    }

    // 3. Specific Log dekhna (GET)
    public function show(string $id)
    {
        $log = ItemMaintenanceLog::with('item')->find($id);

        if (!$log) {
            return response()->json(['message' => 'Maintenance log not found'], 404);
        }

        return response()->json($log, 200);
    }

    // 4. Maintenance status update karna (PUT/PATCH)
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
        return response()->json($log->load('item'), 200);
    }

    // 5. Maintenance Log delete karna (DELETE)
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