<?php

namespace Database\Seeders;

use App\Models\Gemstone;
use Illuminate\Database\Seeder;

class GemstoneSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Diamond',
            'Emerald',
            'Ruby',
            'Sapphire',
            'Pearl',
            'Amethyst',
            'Topaz',
            'Aquamarine',
        ];

        foreach ($items as $index => $name) {
            Gemstone::updateOrCreate(
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
