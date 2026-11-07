<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // A review is meaningless without the product it's about.
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Nullable + set-null-on-delete: preserve the review's
            // content/rating even if the reviewing account is later
            // deleted, rather than destroying the product's review
            // history along with it.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // unsignedTinyInteger bounds this at the type level (0-255).
            // Laravel has no portable cross-driver migration-level CHECK
            // constraint (SQLite in particular doesn't support adding one
            // via ALTER TABLE), so the 1-5 range itself is enforced in
            // the Review model / form validation, not the schema.
            $table->unsignedTinyInteger('rating');

            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'is_approved']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
