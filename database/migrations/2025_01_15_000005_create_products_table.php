<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // A category with existing products must not be silently
            // deletable — restrict forces an explicit re-assignment first.
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();

            // Monetary values as DECIMAL, never float, to avoid
            // rounding/precision issues in pricing and totals.
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();

            // Free-form on purpose (men/women/kids/unisex expected) rather
            // than a DB enum, so new values don't require a migration.
            $table->string('gender', 20)->nullable();

            // Optional descriptive attributes. Nullable because not every
            // piece has a gemstone, a specific occasion, or even a fixed
            // metal type (e.g. mixed-metal or made-to-order pieces).
            // Stored as FKs to the lookup tables rather than free text so
            // the catalog stays normalized and filterable; set null on
            // delete of the lookup row so removing a metal/gemstone/
            // occasion from the admin never blocks or cascades into the
            // product catalog.
            $table->foreignId('metal_type_id')->nullable()->constrained('metal_types')->nullOnDelete();
            $table->foreignId('gemstone_id')->nullable()->constrained('gemstones')->nullOnDelete();
            $table->string('metal_purity')->nullable();
            $table->foreignId('occasion_id')->nullable()->constrained('occasions')->nullOnDelete();

            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_active']);
            $table->index(['is_active', 'is_featured']);
            $table->index('metal_type_id');
            $table->index('gemstone_id');
            $table->index('occasion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
