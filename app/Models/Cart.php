<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

#[Fillable(['user_id', 'session_id'])]
class Cart extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Phase 4: "the current cart" now depends on auth state —
     * the authenticated user's own cart (user_id) when logged in,
     * otherwise the guest cart tied to the current Laravel session
     * (user_id stays null), exactly as Phase 3 defined it. Get-or-create
     * — used only where a cart must exist (adding an item, or merging a
     * guest cart in on login).
     */
    public static function current(): self
    {
        if (Auth::check()) {
            return static::firstOrCreate(['user_id' => Auth::id()]);
        }

        return static::firstOrCreate([
            'session_id' => session()->getId(),
            'user_id' => null,
        ]);
    }

    /**
     * Read-only lookup for the current cart (auth-aware, see current()),
     * without creating one. Used for viewing the cart / the navbar count,
     * so that simply browsing the site never creates empty cart rows.
     */
    public static function findCurrent(): ?self
    {
        if (Auth::check()) {
            return static::where('user_id', Auth::id())->first();
        }

        return static::where('session_id', session()->getId())
            ->whereNull('user_id')
            ->first();
    }

    /**
     * Merge a guest cart's items into this (the authenticated user's)
     * cart, then delete the now-empty guest cart row. Called once, right
     * after login, by AuthController.
     *
     * Rules (mirrors the Phase 3 add() logic so pricing/stock stay
     * consistent with normal cart behaviour):
     * - same product + same variant on both sides → quantities merge
     * - same product + different variant → stays as separate lines
     * - product no longer active, or variant no longer active/valid →
     *   the guest line is dropped rather than carried over
     * - quantity is capped to current stock either way
     * - unit_price is always refreshed to the current server-side price,
     *   never trusted from the stale guest snapshot
     */
    public function mergeFrom(?self $guestCart): void
    {
        if (! $guestCart || $guestCart->id === $this->id) {
            return;
        }

        foreach ($guestCart->items()->with(['product', 'variant'])->get() as $guestItem) {
            $product = $guestItem->product;

            if (! $product || ! $product->is_active) {
                $guestItem->delete();

                continue;
            }

            $variant = $guestItem->variant;

            if ($guestItem->product_variant_id && (! $variant || ! $variant->is_active)) {
                $guestItem->delete();

                continue;
            }

            $stock = $variant ? $variant->stock_quantity : $product->stock_quantity;

            if ($stock <= 0) {
                $guestItem->delete();

                continue;
            }

            $unitPrice = ($variant && $variant->price !== null)
                ? ($variant->sale_price ?? $variant->price)
                : ($product->sale_price ?? $product->price);

            $existing = $this->items()
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min($existing->quantity + $guestItem->quantity, $stock),
                    'unit_price' => $unitPrice,
                ]);
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'cart_id' => $this->id,
                    'quantity' => min($guestItem->quantity, $stock),
                    'unit_price' => $unitPrice,
                ]);
            }
        }

        $guestCart->delete();
    }
}
