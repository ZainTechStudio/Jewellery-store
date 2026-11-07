<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Phase 3: no authentication exists yet, so "the current cart" means
     * the cart tied to the current Laravel session (user_id stays null).
     * Get-or-create — used only where a cart must exist (adding an item).
     */
    public static function current(): self
    {
        return static::firstOrCreate([
            'session_id' => session()->getId(),
            'user_id' => null,
        ]);
    }

    /**
     * Read-only lookup for the current session's cart, without creating
     * one. Used for viewing the cart / the navbar count, so that simply
     * browsing the site never creates empty cart rows.
     */
    public static function findCurrent(): ?self
    {
        return static::where('session_id', session()->getId())
            ->whereNull('user_id')
            ->first();
    }
}
