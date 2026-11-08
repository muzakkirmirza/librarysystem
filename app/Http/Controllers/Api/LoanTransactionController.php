<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanTransaction;
use Illuminate\Http\Request;

class LoanTransactionController extends Controller
{
    // 1. Saari Loan Transactions dekhna (GET)
    public function index()
    {
        $transactions = LoanTransaction::with(['user', 'item', 'request'])->get();
        return response()->json($transactions, 200);
    }

    // 2. Nayi Transaction / Item Issue karna (POST)
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
        return response()->json($transaction->load(['user', 'item', 'request']), 201);
    }

    // 3. Specific Transaction dekhna (GET)
    public function show(string $id)
    {
        $transaction = LoanTransaction::with(['user', 'item', 'request'])->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Loan transaction not found'], 404);
        }

        return response()->json($transaction, 200);
    }

    // 4. Item Return status update karna (PUT/PATCH)
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
        return response()->json($transaction->load(['user', 'item', 'request']), 200);
    }

    // 5. Transaction Delete karna (DELETE)
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