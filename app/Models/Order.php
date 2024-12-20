<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];


    protected $casts = [
        'amount' => 'float',
    ];

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($order) {
            // Ensure 'amount' is numeric before incrementing
            if (is_numeric($order->amount)) {
                $order->tank->increment('level', $order->amount);
            } else {
                throw new \Exception('الكمية المطلوبة يجب ان تكون رقمية');
            }
        });

        static::updated(function ($order) {
            $originalAmount = $order->getOriginal('amount');
            $newAmount = $order->amount;
            $difference = $newAmount - $originalAmount;

            if ($difference != 0) {
                if (is_numeric($difference)) {
                    $order->tank->increment('level', $difference);
                } else {
                    throw new \Exception('الكمية المطلوبة يجب ان تكون رقمية');
                }
            }
        });

        static::deleted(function ($order) {
            if (is_numeric($order->amount)) {
                $order->tank->decrement('level', $order->amount);
            } else {
                throw new \Exception('الكمية المطلوبة يجب ان تكون رقمية');
            }
        });
    }
}
