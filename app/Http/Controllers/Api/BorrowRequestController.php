<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\InventoryItem;
use App\Models\LoanTransaction;
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
            'user_id' => 'nullable|exists:users,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'status' => 'nullable|string|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $validated['user_id'] = $validated['user_id'] ?? $request->user()->id;
        $validated['status'] = $validated['status'] ?? 'pending';

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

        if (($validated['status'] ?? null) === 'approved') {
            InventoryItem::where('id', $borrowRequest->inventory_item_id)->update(['is_available' => false]);

            LoanTransaction::firstOrCreate(
                ['borrow_request_id' => $borrowRequest->id],
                [
                    'user_id' => $borrowRequest->user_id,
                    'inventory_item_id' => $borrowRequest->inventory_item_id,
                    'issue_date' => now()->toDateString(),
                    'due_date' => now()->addDays(7)->toDateString(),
                    'status' => 'issued',
                ]
            );
        }

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