<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Nullable: guest checkout is allowed. Orders are financial
            // history and must never disappear just because a customer
            // account is later deleted — set null, never cascade.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('contact_name');

            // Required: the whole confirmation flow (Phase 6/8) runs
            // over WhatsApp, so a contactable phone number is mandatory
            // regardless of whether the customer has an account.
            $table->string('phone');
            $table->string('email')->nullable();

            $table->enum('status', [
                'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled',
            ])->default('pending');

            // Monetary breakdown — all DECIMAL, never float.
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('USD');

            // Nullable + set-null-on-delete: an order must survive even
            // if the referenced address is later removed from the
            // customer's address book.
            $table->foreignId('shipping_address_id')
                ->nullable()
                ->constrained('addresses')
                ->nullOnDelete();

            // COD / manual only by explicit design — no gateway, no
            // transaction IDs, no online payment status.
            $table->enum('payment_method', ['cod', 'manual'])->default('cod');

            $table->string('coupon_code')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
