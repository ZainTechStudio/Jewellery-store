<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates one initial admin account so there is a safe, controlled way
     * to access /admin without a public "admin registration" page (which
     * must never exist). Idempotent via updateOrCreate on email, so
     * re-running seeders never duplicates it — it only (re)asserts the
     * role/active flag on this one known account.
     *
     * IMPORTANT: this is a development convenience with an obviously fake
     * password. Change it (or delete this seeder's effect by changing the
     * account's password/email) before any real deployment.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@aurelia.test'],
            [
                'name' => 'Aurelia Admin',
                'phone' => null,
                'password' => Hash::make('AureliaAdmin!2026'),
                'is_active' => true,
                'role' => 'admin',
            ]
        );
    }
}
