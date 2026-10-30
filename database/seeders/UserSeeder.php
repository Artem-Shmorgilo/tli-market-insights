<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'collector@tli-market-insights.local'],
            [
                'name' => 'Price Collector',
                'password' => Str::random(32),
            ],
        );
    }
}
