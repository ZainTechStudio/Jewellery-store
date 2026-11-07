<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_templates', function (Blueprint $table) {
            $table->id();
            $table->string('status_key')->unique(); // e.g. order_created, confirmed, shipped
            $table->text('message_template'); // supports placeholders e.g. {order_number}, {customer_name}, {total}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_templates');
    }
};
