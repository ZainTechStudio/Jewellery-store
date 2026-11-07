<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Line items have no meaning without their order.
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // Nullable + set-null-on-delete on purpose: this is only a
            // convenience link back to the live catalog row (e.g. "view
            // this product again"). It must NEVER be relied on to render
            // the order — every fact needed to display this line item
            // historically is snapshotted onto this row below, so the
            // order stays accurate even if the product is renamed,
            // repriced, or deleted entirely.
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            // --- snapshot columns: the historical record of the sale ---
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->json('product_options')->nullable(); // snapshot of the variant's options at time of sale
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
