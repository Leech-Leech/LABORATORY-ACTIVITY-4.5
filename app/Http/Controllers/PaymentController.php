<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function store(Request $request, Order $order, PaymentService $payments): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(Payment::METHODS)],
            'amount' => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
            'payment_reference' => ['nullable', 'string', 'max:100', 'unique:payments,payment_reference'],
        ]);

        $payments->record($order, $data['payment_method'], $data['amount'], $data['payment_reference'] ?? null);

        return back()->with('status', 'Payment recorded.');
    }
}
