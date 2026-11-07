<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id', 'name', 'slug', 'sku', 'short_description', 'description',
    'price', 'sale_price', 'gender', 'metal_type_id', 'gemstone_id', 'metal_purity',
    'occasion_id', 'stock_quantity', 'is_active', 'is_featured', 'sort_order',
])]
class Product extends Model
{
    use SoftDeletes;

    /**
     * Phase 2: the storefront route is `/product/{product:slug}`. Without
     * this override, route('product.show', $product) would generate URLs
     * using the model's id (Laravel's default route key) instead of its
     * slug — every product link on the site would 404. This makes both
     * inbound route-model binding and outbound route() URL generation
     * consistently use the slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Phase 2 (storefront integration) addition: `price`/`sale_price` are
     * cast to `decimal:2` strings by Eloquent, and the Blade views need a
     * "$1,234" style display string identical to the old front-end's
     * money() helper. Small display-only accessors — no schema change,
     * no business logic beyond formatting.
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(get: fn () => '$'.number_format((float) $this->price));
    }

    protected function formattedSalePrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sale_price !== null ? '$'.number_format((float) $this->sale_price) : null
        );
    }

    protected function hasDiscount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sale_price !== null && (float) $this->sale_price < (float) $this->price
        );
    }

    /**
     * Deterministic primary image: the row flagged is_primary, falling
     * back to the first image by sort_order. Only resolves from an
     * already eager-loaded `images` relation (returns null otherwise)
     * so accessing this never triggers a surprise N+1 query.
     */
    protected function primaryImage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('images')
                ? ($this->images->firstWhere('is_primary', true) ?? $this->images->first())
                : null
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function metalType(): BelongsTo
    {
        return $this->belongsTo(MetalType::class);
    }

    public function gemstone(): BelongsTo
    {
        return $this->belongsTo(Gemstone::class);
    }

    public function occasion(): BelongsTo
    {
        return $this->belongsTo(Occasion::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
