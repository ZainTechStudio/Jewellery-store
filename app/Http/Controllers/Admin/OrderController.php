<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * The actual statuses this schema defines (orders.status enum, see
     * the Phase 1 migration) — not reinventing a new status set.
     */
    private const STATUSES = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $query = Order::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }

        $orders = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => self::STATUSES,
            'filters' => ['q' => $search, 'status' => $status],
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'shippingAddress', 'user:id,name,email,phone']);

        return view('admin.orders.show', ['order' => $order, 'statuses' => self::STATUSES]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Order status updated to '.ucfirst($validated['status']).'.');
    }
}
