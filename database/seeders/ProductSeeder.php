<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Gemstone;
use App\Models\MetalType;
use App\Models\Occasion;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seeds 12 realistic jewellery products spanning 11 different
     * categories, using the metal_type / gemstone / occasion lookup
     * tables via their foreign keys (never storing the attribute name
     * directly on the product).
     *
     * Images reuse the placeholder files already generated in Phase 0
     * (public/img/p1.jpg ... p6.jpg) since no real product photography
     * exists yet — only 6 placeholder files exist, so they are cycled
     * across the 12 products with per-product alt_text/sort_order/
     * is_primary metadata. Real photography can replace these files
     * later without touching this seeder.
     *
     * Variants are seeded for the products where a size/length option
     * is realistic (rings, chain lengths, bracelet sizes) using the
     * product_variants.options JSON column — no new attribute tables.
     *
     * Idempotent: products are matched on the unique `sku` column via
     * updateOrCreate(); their images/variants are matched on
     * (product_id, image_path) and (product_id, sku) respectively, so
     * re-running this seeder updates existing rows instead of
     * duplicating them.
     */
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');
        $metals = MetalType::pluck('id', 'slug');
        $gemstones = Gemstone::pluck('id', 'slug');
        $occasions = Occasion::pluck('id', 'slug');

        $products = [
            [
                'name' => 'Solitaire Diamond Engagement Ring',
                'sku' => 'AUR-ENG-001',
                'category' => 'engagement-rings',
                'short_description' => 'A single brilliant-cut diamond raised on a hand-forged 18k gold band.',
                'description' => 'A single brilliant-cut stone raised on a hand-forged 18k gold band. The setting is filed and polished by one goldsmith from start to finish — no two are identical.',
                'price' => 4850.00,
                'sale_price' => null,
                'gender' => 'women',
                'metal' => 'gold',
                'gemstone' => 'diamond',
                'purity' => '18k',
                'occasion' => 'engagement',
                'stock' => 12,
                'featured' => true,
                'images' => ['p1.jpg', 'p5.jpg'],
                'variants' => [
                    ['ring_size' => '6'], ['ring_size' => '7'], ['ring_size' => '8'],
                ],
            ],
            [
                'name' => 'Emerald Royale Statement Ring',
                'sku' => 'AUR-ENG-002',
                'category' => 'engagement-rings',
                'short_description' => 'A rare emerald centrepiece framed by a hand-set 18k gold halo.',
                'description' => 'A limited-series piece built around a certified emerald, hand-set in an 18k gold halo mount. Released in small numbers each season.',
                'price' => 8450.00,
                'sale_price' => 7999.00,
                'gender' => 'women',
                'metal' => 'gold',
                'gemstone' => 'emerald',
                'purity' => '18k',
                'occasion' => 'engagement',
                'stock' => 5,
                'featured' => true,
                'images' => ['p5.jpg'],
                'variants' => [
                    ['ring_size' => '6'], ['ring_size' => '7'],
                ],
            ],
            [
                'name' => 'Eternal Vow Wedding Band',
                'sku' => 'AUR-WED-001',
                'category' => 'wedding-rings',
                'short_description' => 'A clean-lined platinum band for daily wear after the ceremony.',
                'description' => 'A comfort-fit platinum band, hand-polished to a soft satin finish. Designed to sit flush against an engagement ring for everyday wear.',
                'price' => 3200.00,
                'sale_price' => null,
                'gender' => 'unisex',
                'metal' => 'platinum',
                'gemstone' => null,
                'purity' => 'PT950',
                'occasion' => 'wedding',
                'stock' => 20,
                'featured' => false,
                'images' => ['p1.jpg'],
                'variants' => [
                    ['ring_size' => '7'], ['ring_size' => '8'], ['ring_size' => '9'],
                ],
            ],
            [
                'name' => 'Daily Shine Gold Band',
                'sku' => 'AUR-DLY-001',
                'category' => 'daily-wear',
                'short_description' => 'A slim 22k gold band built for everyday wear.',
                'description' => 'A slim, stackable 22k gold band, finished with a high polish that holds up to daily wear.',
                'price' => 1450.00,
                'sale_price' => null,
                'gender' => 'women',
                'metal' => 'gold',
                'gemstone' => null,
                'purity' => '22k',
                'occasion' => 'daily-wear',
                'stock' => 30,
                'featured' => false,
                'images' => ['p1.jpg'],
                'variants' => [
                    ['ring_size' => '6'], ['ring_size' => '7'], ['ring_size' => '8'],
                ],
            ],
            [
                'name' => 'Champagne Halo Pendant Necklace',
                'sku' => 'AUR-PND-001',
                'category' => 'pendant-sets',
                'short_description' => 'A champagne diamond pendant in a rose gold halo, on a fine chain.',
                'description' => 'A champagne-cut diamond set in a rose gold halo, suspended from a fine cable chain. Sold with an adjustable chain length.',
                'price' => 3290.00,
                'sale_price' => null,
                'gender' => 'women',
                'metal' => 'rose-gold',
                'gemstone' => 'diamond',
                'purity' => '18k',
                'occasion' => 'gift',
                'stock' => 15,
                'featured' => true,
                'images' => ['p2.jpg', 'p6.jpg'],
                'variants' => [
                    ['chain_length_in' => 16], ['chain_length_in' => 18], ['chain_length_in' => 20],
                ],
            ],
            [
                'name' => 'Rani Haar Heritage Necklace',
                'sku' => 'AUR-NCK-001',
                'category' => 'rani-haar',
                'short_description' => 'A multi-strand pearl and 22k gold rani haar for bridal occasions.',
                'description' => 'A traditional multi-strand rani haar, hand-strung with cultured pearls on 22k gold, built for bridal and heirloom wear.',
                'price' => 6800.00,
                'sale_price' => null,
                'gender' => 'women',
                'metal' => 'gold',
                'gemstone' => 'pearl',
                'purity' => '22k',
                'occasion' => 'bridal',
                'stock' => 4,
                'featured' => true,
                'images' => ['p6.jpg', 'p2.jpg'],
                'variants' => [
                    ['chain_length_in' => 20], ['chain_length_in' => 22],
                ],
            ],
            [
                'name' => 'Ivory Pearl Choker',
                'sku' => 'AUR-CHK-001',
                'category' => 'chokers',
                'short_description' => 'A close-fitting cultured pearl choker on a sterling silver clasp.',
                'description' => 'A close-fitting strand of cultured pearls finished with a sterling silver clasp — an evening staple.',
                'price' => 1980.00,
                'sale_price' => 1690.00,
                'gender' => 'women',
                'metal' => 'silver',
                'gemstone' => 'pearl',
                'purity' => null,
                'occasion' => 'party',
                'stock' => 18,
                'featured' => false,
                'images' => ['p6.jpg'],
                'variants' => [
                    ['chain_length_in' => 14], ['chain_length_in' => 15],
                ],
            ],
            [
                'name' => 'Lumière Diamond Drop Earrings',
                'sku' => 'AUR-EAR-001',
                'category' => 'earrings',
                'short_description' => 'Brilliant-cut diamond drops in a hand-forged 18k gold setting.',
                'description' => 'A pair of brilliant-cut diamond drop earrings, hand-set in 18k gold — a quiet kind of brilliance for evening wear.',
                'price' => 2740.00,
                'sale_price' => 2470.00,
                'gender' => 'women',
                'metal' => 'gold',
                'gemstone' => 'diamond',
                'purity' => '18k',
                'occasion' => 'party',
                'stock' => 10,
                'featured' => true,
                'images' => ['p3.jpg'],
                'variants' => [],
            ],
            [
                'name' => 'Golden Jhumka Earrings',
                'sku' => 'AUR-JHM-001',
                'category' => 'jhumka',
                'short_description' => 'Traditional bell-shaped 22k gold jhumka earrings.',
                'description' => 'Classic bell-shaped jhumka earrings, hand-finished in 22k gold — a festive-season staple.',
                'price' => 1890.00,
                'sale_price' => null,
                'gender' => 'women',
                'metal' => 'gold',
                'gemstone' => null,
                'purity' => '22k',
                'occasion' => 'festive',
                'stock' => 25,
                'featured' => false,
                'images' => ['p3.jpg'],
                'variants' => [],
            ],
            [
                'name' => 'Aurelia Tennis Bracelet',
                'sku' => 'AUR-BRC-001',
                'category' => 'bangles-bracelets',
                'short_description' => 'A continuous line of diamonds set in white gold.',
                'description' => 'A signature line of brilliant-cut diamonds, hand-set in an 18k white gold tennis bracelet with a secure box clasp.',
                'price' => 6980.00,
                'sale_price' => null,
                'gender' => 'women',
                'metal' => 'white-gold',
                'gemstone' => 'diamond',
                'purity' => '18k',
                'occasion' => 'party',
                'stock' => 6,
                'featured' => true,
                'images' => ['p4.jpg', 'p5.jpg'],
                'variants' => [
                    ['bracelet_size' => 'S'], ['bracelet_size' => 'M'], ['bracelet_size' => 'L'],
                ],
            ],
            [
                'name' => 'Sacred Mangalsutra Classic',
                'sku' => 'AUR-MNG-001',
                'category' => 'mangalsutra',
                'short_description' => 'A traditional black-bead and 22k gold mangalsutra.',
                'description' => 'A traditional mangalsutra design in 22k gold with black beads, hand-finished by our atelier for daily and wedding wear.',
                'price' => 2450.00,
                'sale_price' => null,
                'gender' => 'women',
                'metal' => 'gold',
                'gemstone' => null,
                'purity' => '22k',
                'occasion' => 'wedding',
                'stock' => 14,
                'featured' => false,
                'images' => ['p2.jpg'],
                'variants' => [
                    ['chain_length_in' => 16], ['chain_length_in' => 18],
                ],
            ],
            [
                'name' => 'Little Sparkle Kids Stud Set',
                'sku' => 'AUR-KID-001',
                'category' => 'kids-jewellery',
                'short_description' => 'Lightweight 18k gold stud earrings sized for children.',
                'description' => 'A lightweight pair of 18k gold stud earrings with secure backs, sized and weighted for younger ears.',
                'price' => 890.00,
                'sale_price' => null,
                'gender' => 'kids',
                'metal' => 'gold',
                'gemstone' => null,
                'purity' => '18k',
                'occasion' => 'gift',
                'stock' => 40,
                'featured' => false,
                'images' => ['p3.jpg'],
                'variants' => [],
            ],
        ];

        foreach ($products as $sortOrder => $data) {
            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => $categories[$data['category']],
                    'name' => $data['name'],
                    'slug' => str($data['name'])->slug(),
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'gender' => $data['gender'],
                    'metal_type_id' => $metals[$data['metal']] ?? null,
                    'gemstone_id' => $data['gemstone'] ? ($gemstones[$data['gemstone']] ?? null) : null,
                    'metal_purity' => $data['purity'],
                    'occasion_id' => $data['occasion'] ? ($occasions[$data['occasion']] ?? null) : null,
                    'stock_quantity' => $data['stock'],
                    'is_active' => true,
                    'is_featured' => $data['featured'],
                    'sort_order' => ($sortOrder + 1) * 10,
                ]
            );

            foreach ($data['images'] as $imgIndex => $file) {
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'image_path' => '/img/'.$file],
                    [
                        'alt_text' => $data['name'].' — view '.($imgIndex + 1),
                        'sort_order' => $imgIndex * 10,
                        'is_primary' => $imgIndex === 0,
                    ]
                );
            }

            foreach ($data['variants'] as $varIndex => $options) {
                $optionKey = array_key_first($options);
                $optionValue = array_values($options)[0];
                $variantSku = $data['sku'].'-'.strtoupper(str_replace('_', '', substr($optionKey, 0, 2))).$optionValue;

                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'sku' => $variantSku],
                    [
                        'name' => ucfirst(str_replace('_', ' ', $optionKey)).' '.$optionValue,
                        'options' => $options,
                        'price' => null,
                        'sale_price' => null,
                        'stock_quantity' => max(1, intdiv($data['stock'], max(1, count($data['variants'])))),
                        'is_active' => true,
                        'sort_order' => $varIndex * 10,
                    ]
                );
            }
        }
    }
}
