<?php

namespace Database\Seeders;

use App\Models\League;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Standard',
                'system_name' => 'league_standard',
                'started_at' => null,
            ],
            [
                'name' => 'Tartarus',
                'system_name' => 'league_tartarus',
                'started_at' => '2026-10-16 02:00:00',
            ],
        ];

        foreach ($categories as $category) {
            League::updateOrCreate(
                ['system_name' => $category['system_name']],
                [
                    'name' => $category['name'],
                    'started_at' => $category['started_at'],
                ],
            );
        }
    }
}
