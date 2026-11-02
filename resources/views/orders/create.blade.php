<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New order</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('orders.store') }}" class="p-6 space-y-6"
                      x-data="{ rows: {{ Js::from(old('items', [['product_id' => '', 'quantity' => 1]])) }} }">
                    @csrf

                    @if ($errors->any())
                        <div class="p-4 bg-red-50 text-red-700 rounded-md text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                        <select id="customer_id" name="customer_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select a customer…</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3">
                        <p class="block text-sm font-medium text-gray-700">Products</p>
                        <p class="text-xs text-gray-500">Prices shown are for reference; the server always uses the current database price.</p>

                        <template x-for="(row, i) in rows" :key="i">
                            <div class="flex gap-3 items-end">
                                <select :name="`items[${i}][product_id]`" x-model="row.product_id"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm">
                                    <option value="">Select a product…</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} — {{ number_format($product->price, 2) }}</option>
                                    @endforeach
                                </select>
                                <input type="number" min="1" :name="`items[${i}][quantity]`" x-model="row.quantity"
                                       class="w-24 rounded-md border-gray-300 shadow-sm" aria-label="Quantity">
                                <button type="button" x-show="rows.length > 1" @click="rows.splice(i, 1)"
                                        class="text-sm text-red-600 hover:underline">Remove</button>
                            </div>
                        </template>

                        <button type="button" @click="rows.push({ product_id: '', quantity: 1 })"
                                class="text-sm text-indigo-600 hover:underline">+ Add another product</button>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('orders.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                        <button type="submit"
                                class="px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Create order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
