<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $orders = Order::query()
            ->with(['user', 'items'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('order_number', 'LIKE', "%{$search}%")
                        ->orWhere('customer_name', 'LIKE', "%{$search}%")
                        ->orWhere('customer_email', 'LIKE', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('placed_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.shop.orders', [
            'orders' => $orders,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function update(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $newStatus = $request->validated('status');

        if ($order->status === $newStatus) {
            return back()->with('info', 'El pedido ya tenía ese estado.');
        }

        DB::transaction(function () use ($order, $newStatus): void {
            $order->update(['status' => $newStatus]);

            $order->load('items.car');

            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->car !== null) {
                        $item->car->update(['status' => 'available']);
                    }
                }

                return;
            }

            if ($newStatus === 'completed') {
                foreach ($order->items as $item) {
                    if ($item->car !== null) {
                        $item->car->update(['status' => 'sold']);
                    }
                }

                return;
            }

            if ($newStatus === 'confirmed') {
                foreach ($order->items as $item) {
                    if ($item->car !== null) {
                        $item->car->update(['status' => 'reserved']);
                    }
                }
            }
        });

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
