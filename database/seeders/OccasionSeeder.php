<?php

namespace Database\Seeders;

use App\Models\Occasion;
use Illuminate\Database\Seeder;

class OccasionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Wedding',
            'Engagement',
            'Bridal',
            'Party',
            'Daily Wear',
            'Gift',
            'Festive',
            'Anniversary',
        ];

        foreach ($items as $index => $name) {
            Occasion::updateOrCreate(
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
