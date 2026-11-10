<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Http\Resources\BorrowRequestResource;
use Illuminate\Http\Request;

class BorrowRequestController extends Controller
{
    public function index()
    {
        return BorrowRequestResource::collection(BorrowRequest::with(['user', 'item'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $borrowRequest = BorrowRequest::create($validated);
        return new BorrowRequestResource($borrowRequest->load(['user', 'item']));
    }

    public function show(string $id)
    {
        $borrowRequest = BorrowRequest::with(['user', 'item'])->find($id);

        if (!$borrowRequest) {
            return response()->json(['message' => 'Borrow request not found'], 404);
        }

        return new BorrowRequestResource($borrowRequest);
    }

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
        return new BorrowRequestResource($borrowRequest->load(['user', 'item']));
    }

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