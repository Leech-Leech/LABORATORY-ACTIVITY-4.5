<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['customer', 'staff'])
            ->latest('order_date')
            ->latest('id')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreOrderRequest $request, OrderService $orders): RedirectResponse
    {
        $order = $orders->create(
            (int) $request->validated('customer_id'),
            (int) $request->user()->id,          // staff = the logged-in user
            $request->validated('items'),
        );

        return redirect()->route('orders.show', $order)->with('status', 'Order created.');
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'staff', 'items.product', 'payments']);

        return view('orders.show', compact('order'));
    }
}
