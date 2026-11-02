@php
    use App\Support\Money;
    $balance = $order->balanceCents();
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order {{ $order->order_number }}</h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 hover:underline">← All orders</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Customer</span><br>{{ $order->customer->name }}</div>
                <div><span class="text-gray-500">Staff</span><br>{{ $order->staff->name }}</div>
                <div><span class="text-gray-500">Date</span><br>{{ $order->order_date->format('Y-m-d H:i') }}</div>
                <div><span class="text-gray-500">Status</span><br>{{ ucfirst($order->status) }}</div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3 text-right">Qty</th>
                            <th class="px-6 py-3 text-right">Unit price</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-6 py-3">{{ $item->product->name }}</td>
                                <td class="px-6 py-3 text-right">{{ $item->quantity }}</td>
                                <td class="px-6 py-3 text-right tabular-nums">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-3 text-right tabular-nums">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right">Total</td>
                            <td class="px-6 py-3 text-right tabular-nums">{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-gray-800">Payments</h3>

                @forelse ($order->payments as $payment)
                    <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                        <span>{{ $payment->payment_reference }} · {{ str_replace('_', ' ', $payment->payment_method) }} · {{ $payment->paid_at->format('Y-m-d H:i') }}</span>
                        <span class="tabular-nums">{{ number_format($payment->amount, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No payments recorded.</p>
                @endforelse

                <p class="text-sm font-medium">Balance due: <span class="tabular-nums">{{ Money::fromCents($balance) }}</span></p>

                @if ($balance > 0 && $order->status !== 'cancelled')
                    <form method="POST" action="{{ route('orders.payments.store', $order) }}" class="flex flex-wrap gap-3 items-end pt-2">
                        @csrf
                        <div>
                            <label for="payment_method" class="block text-xs text-gray-500">Method</label>
                            <select id="payment_method" name="payment_method" class="rounded-md border-gray-300 shadow-sm text-sm">
                                @foreach (\App\Models\Payment::METHODS as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ str_replace('_', ' ', $method) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="amount" class="block text-xs text-gray-500">Amount</label>
                            <input id="amount" name="amount" type="number" step="0.01" min="0.01" required
                                   value="{{ old('amount') }}" class="w-32 rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label for="payment_reference" class="block text-xs text-gray-500">Reference (optional)</label>
                            <input id="payment_reference" name="payment_reference" type="text" maxlength="100"
                                   value="{{ old('payment_reference') }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <button type="submit"
                                class="px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Record payment
                        </button>
                    </form>

                    @if ($errors->any())
                        <ul class="text-sm text-red-600 list-disc list-inside">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
