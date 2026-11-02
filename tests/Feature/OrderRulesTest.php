<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderRulesTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private Customer $customer;
    private Product $a;
    private Product $b;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Test']);
        $this->staff = User::factory()->create();
        $this->customer = Customer::create(['name' => 'Test Customer']);
        $this->a = Product::create(['category_id' => $category->id, 'name' => 'A', 'price' => '10.50', 'stock_quantity' => 20]);
        $this->b = Product::create(['category_id' => $category->id, 'name' => 'B', 'price' => '4.25', 'stock_quantity' => 20]);
    }

    private function makeOrder(): Order
    {
        return app(OrderService::class)->create($this->customer->id, $this->staff->id, [
            ['product_id' => $this->a->id, 'quantity' => 2],
            ['product_id' => $this->b->id, 'quantity' => 1],
        ]);
    }

    public function test_totals_are_computed_on_the_server_and_unit_price_is_preserved(): void
    {
        $order = $this->makeOrder();

        $this->assertSame('25.25', $order->total_amount);   // 2 x 10.50 + 1 x 4.25
        $this->assertSame('21.00', $order->items->firstWhere('product_id', $this->a->id)->subtotal);

        $this->a->update(['price' => '99.00']);              // later price change

        $this->assertSame('10.50', OrderItem::where('order_id', $order->id)
            ->where('product_id', $this->a->id)->value('unit_price'));
    }

    public function test_order_without_products_is_rejected(): void
    {
        $this->expectException(ValidationException::class);

        try {
            app(OrderService::class)->create($this->customer->id, $this->staff->id, []);
        } finally {
            $this->assertDatabaseCount('orders', 0);
        }
    }

    public function test_failed_order_rolls_back_completely(): void
    {
        try {
            app(OrderService::class)->create($this->customer->id, $this->staff->id, [
                ['product_id' => $this->a->id, 'quantity' => 1],
                ['product_id' => 999999, 'quantity' => 1],
            ]);
            $this->fail('Expected a ValidationException.');
        } catch (ValidationException) {
            // expected
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(20, $this->a->fresh()->stock_quantity);
    }

    public function test_payments_cannot_exceed_order_total(): void
    {
        $order = $this->makeOrder();                          // total 25.25
        $payments = app(PaymentService::class);

        $payments->record($order, 'cash', '20.00');

        try {
            $payments->record($order, 'card', '5.26');
            $this->fail('Overpayment should be rejected.');
        } catch (ValidationException) {
            // expected
        }

        $payments->record($order, 'card', '5.25');

        $this->assertDatabaseCount('payments', 2);
        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_referenced_records_cannot_be_deleted(): void
    {
        $this->makeOrder();

        foreach ([$this->a, $this->customer, $this->staff] as $model) {
            try {
                $model->delete();
                $this->fail(class_basename($model) . ' should not be deletable.');
            } catch (\DomainException) {
                $this->assertModelExists($model);
            }
        }
    }

    public function test_order_pages_require_authentication(): void
    {
        $this->get('/orders')->assertRedirect('/login');
    }

    public function test_posted_prices_are_ignored(): void
    {
        $this->actingAs($this->staff)->post('/orders', [
            'customer_id' => $this->customer->id,
            'items' => [['product_id' => $this->a->id, 'quantity' => 1, 'unit_price' => '0.01']],
        ])->assertRedirect();

        $this->assertSame('10.50', OrderItem::first()->unit_price);
        $this->assertSame($this->staff->id, Order::first()->user_id);
    }
}
