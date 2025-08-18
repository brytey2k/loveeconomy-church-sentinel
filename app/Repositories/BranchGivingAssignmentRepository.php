<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ContributionType;
use App\Models\Branch;
use App\Models\GivingType;
use App\Models\GivingTypeSystem;

class BranchGivingAssignmentRepository
{
    public function __construct(private GivingTypeRepository $givingTypes)
    {
    }

    /**
     * Assign giving types to branch, enforcing ContributionType::CHURCH and auto-assignables.
     *
     * @param Branch $branch
     * @param array<int,string> $incomingGivingTypeKeys
     */
    public function syncGivingTypes(Branch $branch, array $incomingGivingTypeKeys): void
    {
        // Resolve keys -> IDs
        $ids = $this->givingTypes->getIdsForKeys($incomingGivingTypeKeys, createMissing: true);

        // Only CHURCH contribution type for user-selected IDs
        if ($ids !== []) {
            $ids = GivingType::query()
                ->whereIn('id', $ids)
                ->where('contribution_type', ContributionType::CHURCH->value)
                ->pluck('id')
                ->all();
        }

        // Auto-assignable for CHURCH
        $auto = GivingType::query()
            ->where('auto_assignable', true)
            ->where('contribution_type', ContributionType::CHURCH->value)
            ->pluck('id')
            ->all();

        $merged = array_values(array_unique(array_merge($ids, $auto)));

        // Sync branch giving types
        $branch->givingTypes()->sync($merged);

        // Auto-attach assignable & auto-assignable systems for merged church giving types
        if ($merged !== []) {
            $systemIds = GivingTypeSystem::query()
                ->whereIn('giving_type_id', $merged)
                ->where('assignable', true)
                ->where('auto_assignable', true)
                ->pluck('id')
                ->all();

            if ($systemIds !== []) {
                $branch->givingTypeSystems()->syncWithoutDetaching($systemIds);
            }
        } else {
            $branch->givingTypeSystems()->sync([]);
        }
    }
}
