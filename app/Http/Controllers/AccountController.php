<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function index(): View
    {
        return view('account.index', ['user' => Auth::user()]);
    }

    /**
     * Phase 6 Part A: order history, scoped to the authenticated user
     * only. Newest first, paginated, with item count via withCount()
     * (no per-row N+1) rather than loading every item of every order.
     */
    public function orders(): View
    {
        $orders = Auth::user()->orders()
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('account.orders.index', ['orders' => $orders]);
    }

    /**
     * Order detail — uses the same historical order_items snapshot
     * columns Phase 5 created (never live product data). Ownership is
     * checked explicitly; route-model binding alone does not scope by
     * owner, and a mismatch 404s rather than 403s so a guessed order id
     * can't even confirm another customer's order exists.
     */
    public function orderShow(Order $order): View
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        $order->load(['items', 'shippingAddress']);

        return view('account.orders.show', ['order' => $order]);
    }

    public function editProfile(): View
    {
        return view('account.profile', ['user' => Auth::user()]);
    }

    /**
     * Only name/email/phone can ever be set here — role (which doesn't
     * exist as a column) and is_active are never read from the request,
     * even though is_active is in the User model's fillable list (it
     * needs to be, for the admin side in a later phase). Email/phone
     * uniqueness checks ignore the current user's own row.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
        ]);

        $user->update($validated);

        return redirect()->route('account.profile.edit')->with('success', 'Profile updated.');
    }

    public function editPassword(): View
    {
        return view('account.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        // Re-secure the session after a sensitive account change.
        $request->session()->regenerate();

        return redirect()->route('account.password.edit')->with('success', 'Password changed.');
    }
}
