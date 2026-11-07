<?php

namespace Database\Seeders;

use App\Models\MetalType;
use Illuminate\Database\Seeder;

class MetalTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Gold',
            'Silver',
            'Platinum',
            'Rose Gold',
            'White Gold',
        ];

        foreach ($items as $index => $name) {
            MetalType::updateOrCreate(
                ['slug' => str($name)->slug()],
                [
                    'name' => $name,
                    'is_active' => true,
                    'sort_order' => ($index + 1) * 10,
                ]
            );
        }
    }
}
