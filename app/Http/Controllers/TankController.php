<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TankController extends Controller
{
    /**
     * Print the statement for a specific tank.
     *
     * @param  \App\Models\Tank  $tank
     * @return \Illuminate\View\View
     */
    public function print(Tank $tank)
    {
        // Determine the initial level
        $initialLevel = $tank->initial_level ?? $tank->level;

        // Fetch all orders and transactions related to the tank
        $orders = $tank->orders()->with('fuel')->get();
        $transactions = $tank->transactions()->with(['employee', 'car'])->get();

        // Merge orders and transactions into a unified collection
        $combinedEntries = $this->mergeAndCalculateCumulative($orders, $transactions, $initialLevel, $tank->created_at);

        return view('tank.print', compact('tank', 'initialLevel', 'combinedEntries'));
    }

    /**
     * Merge orders and transactions, calculate cumulative levels.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $orders
     * @param  \Illuminate\Database\Eloquent\Collection  $transactions
     * @param  float  $initialLevel
     * @param  \Carbon\Carbon  $createdAt
     * @return \Illuminate\Support\Collection
     */
    private function mergeAndCalculateCumulative($orders, $transactions, $initialLevel, $createdAt): Collection
    {
        // Map orders to a standardized structure
        $mappedOrders = $orders->map(function ($order) {
            return [
                'date' => $order->order_date,
                'type' => 'order',
                'description' => 'طلب من ' . ($order->supplier ?? 'غير محدد'),
                'amount' => $order->amount,
            ];
        });

        // Map transactions to a standardized structure
        $mappedTransactions = $transactions->map(function ($transaction) {
            return [
                'date' => $transaction->created_at,
                'type' => 'transaction',
                'description' => 'معاملة ' . $transaction->number . ' بواسطة ' . ($transaction->employee->name ?? 'غير محدد'),
                'amount' => $transaction->amount,
            ];
        });

        // Merge and sort the collection by date
        $merged = $mappedOrders->merge($mappedTransactions)->sortBy('date');

        // Add an initial entry to represent the starting level
        $initialEntry = [
            'date' => $createdAt->copy()->subSecond(), // Set a date just before the first operation
            'type' => 'initial',
            'description' => 'المستوى الابتدائي',
            'amount' => 0,
        ];

        // Merge initial entry with operations
        $merged = collect([$initialEntry])->merge($merged)->sortBy('date');

        // Calculate cumulative level
        $cumulativeLevel = $initialLevel;
        $merged = $merged->map(function ($entry) use (&$cumulativeLevel) {
            if ($entry['type'] === 'order') {
                $cumulativeLevel += $entry['amount'];
                $entry['cumulative_level'] = $cumulativeLevel;
            } elseif ($entry['type'] === 'transaction') {
                $cumulativeLevel -= $entry['amount'];
                $entry['cumulative_level'] = $cumulativeLevel;
            } else { // 'initial' type
                $entry['cumulative_level'] = $cumulativeLevel;
            }
            return $entry;
        });

        return $merged;
    }
}
