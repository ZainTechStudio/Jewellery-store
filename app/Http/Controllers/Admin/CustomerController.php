<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $query = User::query()->withCount('orders');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.customers.index', ['customers' => $customers, 'filters' => ['q' => $search]]);
    }

    /**
     * No password hash, no raw role-escalation form field here — only a
     * read-only detail view plus the one explicit toggle-active action
     * below, matching the brief's "do not allow arbitrary role escalation
     * from a normal customer edit form" instruction (there is no edit
     * form at all in this phase, by design).
     */
    public function show(User $user): View
    {
        $orders = $user->orders()->orderByDesc('created_at')->take(10)->get(['id', 'order_number', 'status', 'total', 'created_at']);
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        return view('admin.customers.show', ['customer' => $user, 'orders' => $orders, 'addresses' => $addresses]);
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->isAdmin() && $user->is_active && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'You cannot deactivate the only active admin account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Account activated.' : 'Account deactivated.');
    }
}
