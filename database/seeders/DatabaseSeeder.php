<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Catalog lookup tables first (categories, products and their
        // variants all reference these via foreign key).
        $this->call([
            MetalTypeSeeder::class,
            GemstoneSeeder::class,
            OccasionSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
