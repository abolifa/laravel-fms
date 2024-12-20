<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function print(Transaction $transaction)
    {
        // Generate a printable view or PDF of the transaction
        return view('transactions.print', compact('transaction'));
    }




    public function index()
    {
        // Retrieve all transactions with related employee, tank, and car data
        $transactions = Transaction::with(['employee', 'tank', 'car'])->get();

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Retrieve a single transaction with relations
        $transaction = Transaction::with(['employee', 'tank', 'car'])->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the status input
        $validated = $request->validate([
            'status' => 'required|string|in:pending,completed,cancelled',
        ]);

        // Find the transaction
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        // Update the status
        $transaction->status = $validated['status'];
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Transaction status updated successfully',
            'data' => $transaction,
        ]);
    }
}
