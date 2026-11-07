<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            // Variants have no meaning without their product.
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('sku')->nullable()->unique();

            // Human-readable label shown in the UI, e.g. "Size 7",
            // "18in chain / Rose Gold".
            $table->string('name')->nullable();

            // Structured variant attributes (ring size, bracelet size,
            // chain length, weight, etc.) stored as JSON rather than a
            // separate attributes/attribute_values EAV schema — a single
            // jewellery product realistically has a handful of variant
            // axes, so a small JSON map (e.g. {"ring_size":"7"}) covers
            // every case in the spec without the join/query overhead of
            // a full EAV design. See the Phase 1 report for the full
            // rationale.
            $table->json('options')->nullable();

            // Nullable: when not set, the variant falls back to the
            // parent product's price in application logic.
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();

            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
