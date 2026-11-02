<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(OrderService $orders, PaymentService $payments): void
    {
        $staff = User::where('email', 'staff@example.com')->firstOrFail();
        $customers = Customer::orderBy('id')->get();
        $id = fn (string $name) => Product::where('name', $name)->value('id');

        // Order 1: 2 items, paid in full (one payment)
        $o1 = $orders->create($customers[0]->id, $staff->id, [
            ['product_id' => $id('Arabica Coffee Beans 250g'), 'quantity' => 2],
            ['product_id' => $id('Salted Peanuts 200g'), 'quantity' => 3],
        ]);
        $payments->record($o1, 'cash', '25.25');

        // Order 2: 3 items, two partial payments (still pending)
        $o2 = $orders->create($customers[1]->id, $staff->id, [
            ['product_id' => $id('Green Tea Box (20 bags)'), 'quantity' => 4],
            ['product_id' => $id('Dark Chocolate Bar'), 'quantity' => 2],
            ['product_id' => $id('Cotton Dish Towels (3-pack)'), 'quantity' => 1],
        ]);
        $payments->record($o2, 'card', '10.00');
        $payments->record($o2, 'cash', '10.00');

        // Order 3: 2 items, unpaid
        $orders->create($customers[2]->id, $staff->id, [
            ['product_id' => $id('Dish Soap 500ml'), 'quantity' => 2],
            ['product_id' => $id('Cotton Dish Towels (3-pack)'), 'quantity' => 2],
        ]);
    }
}
