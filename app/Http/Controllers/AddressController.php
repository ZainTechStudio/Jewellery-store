<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->orderByDesc('id')->get();

        return view('account.addresses.index', ['addresses' => $addresses]);
    }

    public function create(): View
    {
        return view('account.addresses.form', ['address' => new Address]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($validated) {
            if (! empty($validated['is_default'])) {
                Auth::user()->addresses()->update(['is_default' => false]);
            }

            Auth::user()->addresses()->create($validated);
        });

        // Phase 5: if this address was added from the checkout page (a
        // hidden "redirect" field on the form carries that through), send
        // the customer back to checkout instead of the address book.
        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index')->with('success', 'Address added.');
        }

        return redirect()->route('account.addresses.index')->with('success', 'Address added.');
    }

    public function edit(Address $address): View
    {
        $this->authorizeOwnership($address);

        return view('account.addresses.form', ['address' => $address]);
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeOwnership($address);

        $validated = $this->validated($request);

        DB::transaction(function () use ($validated, $address) {
            if (! empty($validated['is_default'])) {
                Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($validated);
        });

        return redirect()->route('account.addresses.index')->with('success', 'Address updated.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $this->authorizeOwnership($address);

        $address->delete();

        return redirect()->route('account.addresses.index')->with('success', 'Address removed.');
    }

    /**
     * Route-model binding resolves the Address by id regardless of
     * owner — this is the explicit ownership check that must run before
     * any read/write on it. 404 (not 403) so a guessed id doesn't even
     * confirm another customer's address exists.
     */
    private function authorizeOwnership(Address $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(404);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['nullable', 'string', 'max:20'],
            'contact_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
