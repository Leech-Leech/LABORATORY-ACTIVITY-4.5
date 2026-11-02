<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Support\Money;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order and its items atomically.
     *
     * $lines is a list of ['product_id' => int, 'quantity' => int].
     * Prices are NEVER taken from the caller: they are read from the products table.
     */
    public function create(int $customerId, int $staffId, array $lines, ?CarbonInterface $orderDate = null): Order
    {
        $quantities = $this->mergeLines($lines);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'An order must contain at least one product.']);
        }

        return DB::transaction(function () use ($customerId, $staffId, $quantities, $orderDate) {
            // Lock product rows (ordered by id to avoid deadlocks) so price/stock can't change mid-order.
            $products = Product::whereIn('id', $quantities->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $order = Order::create([
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'customer_id' => $customerId,
                'user_id' => $staffId,
                'order_date' => $orderDate ?? now(),
                'status' => Order::STATUS_PENDING,
                'total_amount' => '0.00',
            ]);

            $totalCents = 0;

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);

                if (! $product) {
                    throw ValidationException::withMessages(['items' => "Product #{$productId} does not exist."]);
                }
                if (! $product->is_available) {
                    throw ValidationException::withMessages(['items' => "{$product->name} is not available."]);
                }
                if ($product->stock_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for {$product->name} (only {$product->stock_quantity} left).",
                    ]);
                }

                $unitCents = Money::toCents($product->price);
                $subtotalCents = $unitCents * $quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => Money::fromCents($unitCents),     // snapshot of current price
                    'subtotal' => Money::fromCents($subtotalCents),   // quantity x unit_price
                ]);

                $product->decrement('stock_quantity', $quantity);
                $totalCents += $subtotalCents;
            }

            $order->update(['total_amount' => Money::fromCents($totalCents)]);

            return $order->load(['customer', 'staff', 'items.product']);
        });
    }

    /** Merge duplicate product lines and validate shape. Returns [product_id => quantity]. */
    private function mergeLines(array $lines): Collection
    {
        $merged = collect();

        foreach ($lines as $line) {
            $productId = (int) ($line['product_id'] ?? 0);
            $quantity = (int) ($line['quantity'] ?? 0);

            if ($productId < 1 || $quantity < 1) {
                throw ValidationException::withMessages([
                    'items' => 'Each line needs a product and a quantity of at least 1.',
                ]);
            }

            $merged[$productId] = ($merged[$productId] ?? 0) + $quantity;
        }

        return $merged;
    }
}
