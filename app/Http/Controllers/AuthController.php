<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Create a customer account. There is no role column on the users
     * table (and none is added here — Phase 4 doesn't need one, since
     * every self-registered account is a customer by definition; there
     * is no other registration path and no admin auth in this phase).
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $this->loginAndMergeCart($request, $user);

        return redirect()->route('account.index')->with('success', 'Welcome to Aurelia, '.$user->name.'.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Credentials are checked manually (rather than Auth::attempt())
     * so an inactive account can be rejected before a session is ever
     * started for it.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Those credentials don\'t match our records.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'This account is inactive. Please contact client care.',
            ]);
        }

        $this->loginAndMergeCart($request, $user);

        return redirect()->intended(route('account.index'))->with('success', 'Welcome back, '.$user->name.'.');
    }

    /**
     * Logout invalidates the session and regenerates the CSRF token (so
     * the next request gets a fresh session id) but never touches the
     * cart — the customer's database cart stays intact for next time,
     * and a brand new guest session cart can start fresh if they shop
     * again before logging back in.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been signed out.');
    }

    /**
     * Shared by register() and login(): capture the guest session cart
     * BEFORE regenerating the session (regeneration changes the session
     * id, so the guest cart would become unreachable by session id
     * afterwards), log the user in, then merge the guest cart into
     * their account cart.
     */
    private function loginAndMergeCart(Request $request, User $user): void
    {
        $guestCart = Cart::where('session_id', session()->getId())
            ->whereNull('user_id')
            ->first();

        Auth::login($user, true);
        $request->session()->regenerate();

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);
        $userCart->mergeFrom($guestCart);
    }
}
