<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extends Laravel's default `users` table rather than creating a
     * separate customers table — reuses Laravel's built-in auth
     * conventions (email + password login) as-is. `email` is left
     * required/unique exactly as Laravel's default scaffolding expects,
     * since changing that would break the framework's authentication
     * guard/notification internals. Only what Aurelia actually needs
     * beyond that is added: a WhatsApp-capable phone number and a
     * simple account status flag.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->boolean('is_active')->default(true)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropColumn(['phone', 'is_active']);
        });
    }
};
