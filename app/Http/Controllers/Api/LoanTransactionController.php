<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanTransaction;
use App\Models\InventoryItem;
use App\Http\Resources\LoanTransactionResource;
use Illuminate\Http\Request;

class LoanTransactionController extends Controller
{
    public function index()
    {
        return LoanTransactionResource::collection(LoanTransaction::with(['user', 'item', 'request'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'borrow_request_id' => 'required|exists:borrow_requests,id',
            'user_id' => 'required|exists:users,id',
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'return_date' => 'nullable|date',
            'status' => 'required|string|in:issued,returned,overdue',
        ]);

        $transaction = LoanTransaction::create($validated);

        if ($transaction->status === 'issued') {
            InventoryItem::where('id', $transaction->inventory_item_id)->update(['is_available' => false]);
        }

        return new LoanTransactionResource($transaction->load(['user', 'item', 'request']));
    }

    public function show(string $id)
    {
        $transaction = LoanTransaction::with(['user', 'item', 'request'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Loan transaction not found'], 404);
        }

        return new LoanTransactionResource($transaction);
    }

    public function update(Request $request, string $id)
    {
        $transaction = LoanTransaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Loan transaction not found'], 404);
        }

        $validated = $request->validate([
            'issue_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date',
            'return_date' => 'nullable|date',
            'status' => 'sometimes|required|string|in:issued,returned,overdue',
        ]);

        $transaction->update($validated);

        if ($transaction->status === 'returned') {
            InventoryItem::where('id', $transaction->inventory_item_id)->update(['is_available' => true]);
        } elseif ($transaction->status === 'issued' || $transaction->status === 'overdue') {
            InventoryItem::where('id', $transaction->inventory_item_id)->update(['is_available' => false]);
        }

        return new LoanTransactionResource($transaction->load(['user', 'item', 'request']));
    }

    public function destroy(string $id)
    {
        $transaction = LoanTransaction::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Loan transaction not found'], 404);
        }

        $transaction->delete();
        return response()->json(['message' => 'Loan transaction deleted successfully'], 200);
    }
}