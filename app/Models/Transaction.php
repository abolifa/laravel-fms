<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
        // Add other casts if necessary
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Handle creating a new transaction
        static::creating(function (Transaction $transaction) {
            Log::info('Creating Transaction:', [
                'status' => $transaction->status,
                'amount' => $transaction->amount,
            ]);

            if ($transaction->status === 'مكتمل' && !is_null($transaction->amount)) {
                Log::info('Adjusting values during creating:', [
                    'adjustAmount' => -floatval($transaction->amount),
                ]);
                $transaction->adjustValues(-floatval($transaction->amount));
            }
        });

        // Handle updating an existing transaction
        static::updating(function (Transaction $transaction) {
            $originalStatus = $transaction->getOriginal('status');
            $newStatus = $transaction->status;
            $originalAmount = $transaction->getOriginal('amount');
            $newAmount = $transaction->amount;

            Log::info('Updating Transaction:', [
                'original_status' => $originalStatus,
                'new_status' => $newStatus,
                'original_amount' => $originalAmount,
                'new_amount' => $newAmount,
            ]);

            // If original status was 'مكتمل', revert the original deduction
            if ($originalStatus === 'مكتمل' && !is_null($originalAmount)) {
                Log::info('Reverting original deduction during updating:', [
                    'adjustAmount' => floatval($originalAmount),
                ]);
                $transaction->adjustValues(floatval($originalAmount));
            }

            // If new status is 'مكتمل', apply the new deduction
            if ($newStatus === 'مكتمل' && !is_null($newAmount)) {
                Log::info('Applying new deduction during updating:', [
                    'adjustAmount' => -floatval($newAmount),
                ]);
                $transaction->adjustValues(-floatval($newAmount));
            }

            // Reset 'amount' if the new status is not 'مكتمل'
            if ($newStatus !== 'مكتمل') {
                Log::info('Resetting amount as status is not مكتمل:', [
                    'previous_amount' => $newAmount,
                ]);
                $transaction->amount = null;
            }
        });

        // Handle deleting a transaction
        static::deleting(function (Transaction $transaction) {
            Log::info('Deleting Transaction:', [
                'status' => $transaction->status,
                'amount' => $transaction->amount,
            ]);

            if ($transaction->status === 'مكتمل' && !is_null($transaction->amount)) {
                Log::info('Reverting deduction during deleting:', [
                    'adjustAmount' => floatval($transaction->amount),
                ]);
                $transaction->adjustValues(floatval($transaction->amount));
            }
        });
    }

    /**
     * Adjust tank and employee values by the given amount.
     *
     * @param float $amount Positive to add, negative to deduct.
     */
    protected function adjustValues(float $amount)
    {
        Log::info('Adjusting values:', [
            'amount' => $amount,
            'transaction_id' => $this->id,
        ]);

        $tank = $this->tank;
        $employee = $this->employee;

        if ($tank) {
            $tank->level += $amount; // Add or deduct from tank level
            $tank->save();
            Log::info('Tank level adjusted:', [
                'tank_id' => $tank->id,
                'new_level' => $tank->level,
            ]);
        } else {
            Log::warning('No tank associated with transaction:', [
                'transaction_id' => $this->id,
            ]);
        }

        if ($employee) {
            $employee->quota += $amount; // Add or deduct from employee quota
            $employee->save();
            Log::info('Employee quota adjusted:', [
                'employee_id' => $employee->id,
                'new_quota' => $employee->quota,
            ]);
        } else {
            Log::warning('No employee associated with transaction:', [
                'transaction_id' => $this->id,
            ]);
        }
    }
}
