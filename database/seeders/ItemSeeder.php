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
        $jsonPath = database_path('seeders/data/items_seed.json');

        if (!is_file($jsonPath)) {
            throw new RuntimeException(
                "Items JSON file was not found: {$jsonPath}"
            );
        }

        $data = json_decode(
            file_get_contents($jsonPath),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new RuntimeException(
                'Invalid items JSON: expected an "items" array.'
            );
        }

        // Build a lookup: category system_name => database ID.
        $categories = Category::query()
            ->pluck('id', 'system_name')
            ->all();

        $imported = 0;
        $skipped = 0;

        foreach ($data['items'] as $item) {
            $externalId = trim((string) ($item['id'] ?? ''));
            $name = trim((string) ($item['name_en'] ?? ''));
            $categorySystemName = trim(
                (string) ($item['category'] ?? '')
            );

            if ($externalId === '' || $name === '') {
                $skipped++;
                continue;
            }

            if (
                $categorySystemName === ''
                || !isset($categories[$categorySystemName])
            ) {
                throw new RuntimeException(
                    "Category not found for item {$externalId}: "
                    . ($categorySystemName ?: '(empty)')
                );
            }

            Item::updateOrCreate(
                ['external_id' => $externalId],
                [
                    'name' => $name,
                    'category_id' => $categories[$categorySystemName],
                    'image_path' => $item['img'] ?? null,
                ],
            );

            $imported++;
        }

        $this->command?->info(
            "Items imported or updated: {$imported}"
        );

        if ($skipped > 0) {
            $this->command?->warn(
                "Items skipped due to missing ID or name: {$skipped}"
            );
        }
    }
}
