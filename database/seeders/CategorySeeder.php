<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Legendary Gear',
                'system_name' => 'category_legendary_gear',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Gear',
                'system_name' => 'category_gear',
                'is_hidden' => true,
                'image_path' => null,
            ],
            [
                'name' => 'Hero Memories',
                'system_name' => 'category_hero_memories',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Item Collection',
                'system_name' => 'category_item_collection',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Legendary Divinity Slate',
                'system_name' => 'category_legendary_divinity_slate',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Non-Legendary Divinity Slate',
                'system_name' => 'category_non_legendary_divinity_slate',
                'is_hidden' => true,
                'image_path' => null,
            ],
            [
                'name' => 'Nether King\'s Divinity',
                'system_name' => 'category_nether_kings_divinity',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Destiny',
                'system_name' => 'category_destiny',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Prism',
                'system_name' => 'category_prism',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Vorax',
                'system_name' => 'category_vorax',
                'is_hidden' => true,
                'image_path' => null,
            ],
            [
                'name' => 'Graft Organ',
                'system_name' => 'category_graft_organ',
                'is_hidden' => false,
                'image_path' => null,
            ],
            [
                'name' => 'Follower',
                'system_name' => 'category_follower',
                'is_hidden' => false,
                'image_path' => null,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['system_name' => $category['system_name']],
                [
                    'name' => $category['name'],
                    'is_hidden' => $category['is_hidden'],
                    'image_path' => $category['image_path'],
                ],
            );
        }
    }
}
