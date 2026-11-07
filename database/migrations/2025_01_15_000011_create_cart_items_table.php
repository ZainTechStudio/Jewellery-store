<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // A cart item has no meaning without its cart.
            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();

            // Cart contents are disposable pre-order data (unlike
            // order_items, which must survive product changes/deletion
            // for historical accuracy) — cascading here just cleans the
            // line out of the cart if the product is ever hard-deleted.
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Nullable: not every product has variants. Set null on
            // delete rather than cascade, so removing a specific variant
            // degrades the line to the base product instead of wiping it
            // from the cart outright.
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            // Price captured at the moment the item was added, so cart
            // totals stay stable even if the product's live price
            // changes while it sits in the bag.
            $table->decimal('unit_price', 10, 2);

            $table->timestamps();

            $table->index('cart_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
