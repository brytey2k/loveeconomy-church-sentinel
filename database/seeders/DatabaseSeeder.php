<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Bright Nkrumah',
            'email' => 'brytey2k@gmail.com',
        ]);

        // Seed baseline tags if missing
        foreach ([
            ['key' => 'partner', 'name' => 'Partner'],
            ['key' => 'tither', 'name' => 'Tither'],
        ] as $seedTag) {
            Tag::query()->firstOrCreate(['key' => $seedTag['key']], $seedTag);
        }
    }
}
