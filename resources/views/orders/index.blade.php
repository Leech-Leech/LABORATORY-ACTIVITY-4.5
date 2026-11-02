<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Orders</h2>
            <a href="{{ route('orders.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                New order
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Order #</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Staff</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-6 py-3 font-medium">
                                    <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:underline">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-6 py-3">{{ $order->customer->name }}</td>
                                <td class="px-6 py-3">{{ $order->staff->name }}</td>
                                <td class="px-6 py-3 text-right tabular-nums">{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-6 py-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        'bg-yellow-100 text-yellow-800' => $order->status === 'pending',
                                        'bg-green-100 text-green-800' => $order->status === 'paid',
                                        'bg-gray-200 text-gray-700' => $order->status === 'cancelled',
                                    ])>{{ ucfirst($order->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
