<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContributionType;
use App\Models\Branch;
use App\Models\Country;
use App\Models\GivingType;
use App\Models\Level;
use App\Models\Member;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Admin user (example)
        User::factory()->create([
            'name' => 'Bright Nkrumah',
            'email' => 'brytey2k@gmail.com',
        ]);

        // 2) Baseline giving types
        foreach ([
            ['key' => 'partnership', 'name' => 'Partnership', 'contribution_type' => ContributionType::INDIVIDUAL->value],
            ['key' => 'tithing', 'name' => 'Tithing', 'contribution_type' => ContributionType::INDIVIDUAL->value],
            ['key' => 'offering', 'name' => 'Offering', 'contribution_type' => ContributionType::CHURCH->value],
            ['key' => 'first_fruit', 'name' => 'First Fruit', 'contribution_type' => ContributionType::INDIVIDUAL->value],
            ['key' => 'seed_sowing', 'name' => 'Seed Sowing', 'contribution_type' => ContributionType::INDIVIDUAL->value],
        ] as $seed) {
            GivingType::query()->firstOrCreate(['key' => $seed['key']], $seed);
        }

        // 3) Levels (with ordered positions)
        $levels = [
            ['name' => 'Head Office', 'position' => 1],
            ['name' => 'Zone', 'position' => 2],
            ['name' => 'Branch', 'position' => 3],
        ];
        foreach ($levels as $lvl) {
            Level::query()->firstOrCreate(
                ['name' => $lvl['name']],
                $lvl
            );
        }
        $levelIds = Level::query()->pluck('id', 'name');

        // 4) Countries
        $countries = ['Ghana', 'UK', 'Canada', 'Kenya', 'Nigeria', 'US'];
        foreach ($countries as $country) {
            Country::query()->firstOrCreate(['name' => $country]);
        }
        $countryIds = Country::query()->pluck('id', 'name');

        // We'll place the hierarchy under Ghana unless stated otherwise
        $ghanaId = (int) $countryIds['Ghana'];

        // 5) Positions
        $positions = ['Pastor', 'MC Head', 'Cell Shepherd', 'Shepherd', 'Member'];
        foreach ($positions as $posName) {
            Position::query()->firstOrCreate(['name' => $posName]);
        }
        $positionIds = Position::query()->pluck('id', 'name');

        // 6) Branch hierarchy (using LTree paths built from IDs)
        // Head Office
        $thesaurus = Branch::query()->firstOrCreate(
            ['name' => 'Thesaurus'],
            [
                'level_id' => (int) $levelIds['Head Office'],
                'country_id' => $ghanaId,
                'parent_id' => null,
                'path' => '0',
            ]
        );
        // Ensure ID-based path for Thesaurus
        $thesaurusPath = (string) $thesaurus->id;
        if ($thesaurus->path !== $thesaurusPath) {
            $thesaurus->update(['path' => $thesaurusPath]);
        }

        // Zones under Thesaurus
        $eugeneZone = Branch::query()->firstOrCreate(
            ['name' => 'Eugene Zone', 'parent_id' => $thesaurus->id],
            [
                'level_id' => (int) $levelIds['Zone'],
                'country_id' => $ghanaId,
                'parent_id' => $thesaurus->id,
                'path' => '0',
            ]
        );
        $eugenePath = $thesaurus->id . '.' . $eugeneZone->id;
        if ($eugeneZone->path !== $eugenePath) {
            $eugeneZone->update(['path' => $eugenePath]);
        }

        $elikemZone = Branch::query()->firstOrCreate(
            ['name' => 'Elikem Zone', 'parent_id' => $thesaurus->id],
            [
                'level_id' => (int) $levelIds['Zone'],
                'country_id' => $ghanaId,
                'parent_id' => $thesaurus->id,
                'path' => '0',
            ]
        );
        $elikemPath = $thesaurus->id . '.' . $elikemZone->id;
        if ($elikemZone->path !== $elikemPath) {
            $elikemZone->update(['path' => $elikemPath]);
        }

        // Branches under Eugene Zone
        $photizo = Branch::query()->firstOrCreate(
            ['name' => 'Photizo', 'parent_id' => $eugeneZone->id],
            [
                'level_id' => (int) $levelIds['Branch'],
                'country_id' => $ghanaId,
                'parent_id' => $eugeneZone->id,
                'path' => '0',
            ]
        );
        $photizoPath = $thesaurus->id . '.' . $eugeneZone->id . '.' . $photizo->id;
        if ($photizo->path !== $photizoPath) {
            $photizo->update(['path' => $photizoPath]);
        }

        $asafo = Branch::query()->firstOrCreate(
            ['name' => 'Asafo', 'parent_id' => $eugeneZone->id],
            [
                'level_id' => (int) $levelIds['Branch'],
                'country_id' => $ghanaId,
                'parent_id' => $eugeneZone->id,
                'path' => '0',
            ]
        );
        $asafoPath = $thesaurus->id . '.' . $eugeneZone->id . '.' . $asafo->id;
        if ($asafo->path !== $asafoPath) {
            $asafo->update(['path' => $asafoPath]);
        }

        // Branches under Elikem Zone
        $botwe = Branch::query()->firstOrCreate(
            ['name' => 'Botwe', 'parent_id' => $elikemZone->id],
            [
                'level_id' => (int) $levelIds['Branch'],
                'country_id' => $ghanaId,
                'parent_id' => $elikemZone->id,
                'path' => '0',
            ]
        );
        $botwePath = $thesaurus->id . '.' . $elikemZone->id . '.' . $botwe->id;
        if ($botwe->path !== $botwePath) {
            $botwe->update(['path' => $botwePath]);
        }

        $aburi = Branch::query()->firstOrCreate(
            ['name' => 'Aburi', 'parent_id' => $elikemZone->id],
            [
                'level_id' => (int) $levelIds['Branch'],
                'country_id' => $ghanaId,
                'parent_id' => $elikemZone->id,
                'path' => '0',
            ]
        );
        $aburiPath = $thesaurus->id . '.' . $elikemZone->id . '.' . $aburi->id;
        if ($aburi->path !== $aburiPath) {
            $aburi->update(['path' => $aburiPath]);
        }

        // 7) Members (~5) with positions and branch assignments
        $membersData = [
            ['first_name' => 'Kofi',   'last_name' => 'Mensah',  'phone' => '233200000001', 'branch' => $photizo, 'position' => 'Pastor'],
            ['first_name' => 'Ama',    'last_name' => 'Owusu',   'phone' => '233200000002', 'branch' => $asafo,   'position' => 'MC Head'],
            ['first_name' => 'Yaw',    'last_name' => 'Boateng', 'phone' => '233200000003', 'branch' => $botwe,   'position' => 'Cell Shepherd'],
            ['first_name' => 'Akua',   'last_name' => 'Asante',  'phone' => '233200000004', 'branch' => $aburi,   'position' => 'Shepherd'],
            ['first_name' => 'Kojo',   'last_name' => 'Adjei',   'phone' => '233200000005', 'branch' => $asafo,   'position' => 'Member'],
        ];

        $givingTypes = GivingType::query()->pluck('id', 'key');

        foreach ($membersData as $m) {
            $member = Member::query()->firstOrCreate(
                ['phone' => $m['phone']],
                [
                    'first_name' => $m['first_name'],
                    'last_name' => $m['last_name'],
                    'branch_id' => $m['branch']->id,
                    'position_id' => (int) $positionIds[$m['position']],
                ]
            );

            // Attach some giving types
            $attachKeys = match ($m['position']) {
                'Pastor' => ['tithing', 'partnership'],
                'MC Head' => ['partnership', 'seed_sowing'],
                'Cell Shepherd' => ['tithing', 'first_fruit'],
                'Shepherd' => ['seed_sowing'],
                default => ['tithing'],
            };

            $ids = collect($attachKeys)
                ->map(static fn ($k) => $givingTypes[$k] ?? null)
                ->filter()
                ->values()
                ->all();
            if ($ids !== []) {
                $member->givingTypes()->syncWithoutDetaching($ids);
            }
        }
    }
}
