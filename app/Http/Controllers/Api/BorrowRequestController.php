<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;

class BorrowRequestController extends Controller
{
    // 1. Saari Borrow Requests dekhna (User aur Item ki details ke sath) (GET)
    public function index()
    {
        $requests = BorrowRequest::with(['user', 'item'])->get();
        return response()->json($requests, 200);
    }

    // 2. Student ki nayi request create karna (POST)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $borrowRequest = BorrowRequest::create($validated);
        return response()->json($borrowRequest->load(['user', 'item']), 201);
    }

    // 3. Kisi specific request ki detail dekhna (GET)
    public function show(string $id)
    {
        $borrowRequest = BorrowRequest::with(['user', 'item'])->find($id);

        if (!$borrowRequest) {
            return response()->json(['message' => 'Borrow request not found'], 404);
        }

        return response()->json($borrowRequest, 200);
    }

    // 4. Request Status Update karna (Approve/Reject karna) (PUT/PATCH)
    public function update(Request $request, string $id)
    {
        $borrowRequest = BorrowRequest::find($id);

        if (!$borrowRequest) {
            return response()->json(['message' => 'Borrow request not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'sometimes|required|string|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $borrowRequest->update($validated);
        return response()->json($borrowRequest->load(['user', 'item']), 200);
    }

    // 5. Request Delete karna (DELETE)
    public function destroy(string $id)
    {
        $borrowRequest = BorrowRequest::find($id);

        if (!$borrowRequest) {
            return response()->json(['message' => 'Borrow request not found'], 404);
        }

        $borrowRequest->delete();
        return response()->json(['message' => 'Borrow request deleted successfully'], 200);
    }
}