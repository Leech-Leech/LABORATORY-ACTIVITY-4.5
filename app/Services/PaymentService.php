<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Support\Money;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Record a payment. The sum of all payments for an order may never exceed its total.
     */
    public function record(
        Order $order,
        string $method,
        string|int|float $amount,
        ?string $reference = null,
        ?CarbonInterface $paidAt = null,
    ): Payment {
        return DB::transaction(function () use ($order, $method, $amount, $reference, $paidAt) {
            // Lock the order row so two simultaneous payments can't both pass the check.
            $order = Order::whereKey($order->getKey())->lockForUpdate()->firstOrFail();

            if ($order->status === Order::STATUS_CANCELLED) {
                throw ValidationException::withMessages(['amount' => 'Cancelled orders cannot receive payments.']);
            }

            $amountCents = Money::toCents($amount);
            if ($amountCents <= 0) {
                throw ValidationException::withMessages(['amount' => 'The payment amount must be greater than zero.']);
            }

            $totalCents = Money::toCents($order->total_amount);
            $paidCents = Money::toCents(Payment::where('order_id', $order->id)->sum('amount'));
            $remaining = $totalCents - $paidCents;

            if ($amountCents > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment exceeds the remaining balance of ' . Money::fromCents($remaining) . '.',
                ]);
            }

            $payment = $order->payments()->create([
                'payment_reference' => $reference ?: 'PAY-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8)),
                'payment_method' => $method,
                'amount' => Money::fromCents($amountCents),
                'paid_at' => $paidAt ?? now(),
            ]);

            if ($paidCents + $amountCents === $totalCents) {
                $order->update(['status' => Order::STATUS_PAID]);
            }

            return $payment;
        });
    }
}
