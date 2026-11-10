<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The users table had no role/admin mechanism before Phase 6 (confirmed
     * by inspecting 0001_01_01_000000_create_users_table.php and
     * 2025_01_15_000008_add_phone_and_status_to_users_table.php — neither
     * adds one). A plain string column (not a DB-level enum) is used for
     * portability across the sqlite/mysql drivers this project has used,
     * with application-level restriction to 'customer'/'admin' values —
     * consistent with how `gender`/address `type` were already handled
     * elsewhere in this schema. Defaulting to 'customer' means every
     * existing row keeps working as a normal customer with zero data
     * migration needed.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('customer')->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
