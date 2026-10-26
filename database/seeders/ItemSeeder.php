<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;
use RuntimeException;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::get()->keyBy('system_name');

        if ($categories->count() === 0) {
            throw new RuntimeException(
                'Categories were not found. '
                . 'Run CategorySeeder first.'
            );
        }

        $items = [
            [
                'external_id' => 'example_item_001',
                'name' => 'Example Legendary Item',
                'image_path' => 'images/items/example-item-001.png',
                'category_id' => $categories['category_legendary_gear']->id,
            ],
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(
                ['external_id' => $item['external_id']],
                [
                    'name' => $item['name'],
                    'category_id' => $item['category_id'],
                    'image_path' => $item['image_path'],
                ],
            );
        }
    }
}
