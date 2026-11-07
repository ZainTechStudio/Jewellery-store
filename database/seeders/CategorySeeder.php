<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seeds the agreed Aurelia category tree (3 parent categories with
     * 3 children each, plus 9 standalone top-level categories = 21 rows
     * total). Uses updateOrCreate() keyed on the unique `slug` column so
     * re-running the seeder updates names/parents instead of duplicating
     * rows.
     */
    public function run(): void
    {
        $tree = [
            ['name' => 'Rings', 'slug' => 'rings', 'children' => [
                ['name' => 'Engagement Rings', 'slug' => 'engagement-rings'],
                ['name' => 'Wedding Rings', 'slug' => 'wedding-rings'],
                ['name' => 'Daily Wear', 'slug' => 'daily-wear'],
            ]],
            ['name' => 'Necklaces', 'slug' => 'necklaces', 'children' => [
                ['name' => 'Chokers', 'slug' => 'chokers'],
                ['name' => 'Rani Haar', 'slug' => 'rani-haar'],
                ['name' => 'Pendant Sets', 'slug' => 'pendant-sets'],
            ]],
            ['name' => 'Earrings', 'slug' => 'earrings', 'children' => [
                ['name' => 'Studs', 'slug' => 'studs'],
                ['name' => 'Jhumka', 'slug' => 'jhumka'],
                ['name' => 'Hoops', 'slug' => 'hoops'],
            ]],
            ['name' => 'Bangles & Bracelets', 'slug' => 'bangles-bracelets'],
            ['name' => 'Anklets / Payal', 'slug' => 'anklets-payal'],
            ['name' => 'Nose Pins / Nath', 'slug' => 'nose-pins-nath'],
            ['name' => 'Mangalsutra', 'slug' => 'mangalsutra'],
            ['name' => 'Maang Tikka', 'slug' => 'maang-tikka'],
            ['name' => 'Brooches', 'slug' => 'brooches'],
            ['name' => "Men's Jewellery", 'slug' => 'mens-jewellery'],
            ['name' => 'Kids Jewellery', 'slug' => 'kids-jewellery'],
            ['name' => 'Bridal Sets', 'slug' => 'bridal-sets'],
        ];

        $sortOrder = 0;

        foreach ($tree as $node) {
            $sortOrder += 10;

            $parent = Category::updateOrCreate(
                ['slug' => $node['slug']],
                [
                    'parent_id' => null,
                    'name' => $node['name'],
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ]
            );

            $childSortOrder = 0;

            foreach ($node['children'] ?? [] as $child) {
                $childSortOrder += 10;

                Category::updateOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'parent_id' => $parent->id,
                        'name' => $child['name'],
                        'is_active' => true,
                        'sort_order' => $childSortOrder,
                    ]
                );
            }
        }
    }
}
