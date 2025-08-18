<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\UpdateBranchGivingTypeSystemsData; // reuse generic DTO for giving_type_ids
use App\Data\UpdateMemberGivingTypesData;
use App\Enums\ContributionType;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GivingType;
use App\Models\GivingTypeSystem;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BranchGivingsController extends Controller
{
    /**
     * Show a page to manage a branch's assigned giving types (church) and systems.
     *
     * @param Branch $branch
     */
    public function show(Branch $branch): Response
    {
        $branch->load(['givingTypes', 'givingTypeSystems']);

        // Only CHURCH giving types that are currently assigned to the branch
        $givingTypes = GivingType::query()
            ->where('contribution_type', ContributionType::CHURCH->value)
            ->whereIn('id', $branch->givingTypes->pluck('id')->all())
            ->orderBy('name')
            ->get();

        $typesPayload = $givingTypes->map(static function (GivingType $type) use ($branch) {
            $systems = GivingTypeSystem::query()
                ->where('giving_type_id', $type->id)
                ->where('assignable', true)
                ->orderBy('name')
                ->get(['id', 'name', 'amount_low', 'amount_high', 'auto_assignable']);

            $attachedIds = $branch->givingTypeSystems
                ->where('giving_type_id', $type->id)
                ->pluck('id')
                ->values();

            return [
                'id' => $type->id,
                'name' => $type->name,
                'key' => $type->key,
                'systems' => $systems,
                'attached_system_ids' => $attachedIds,
            ];
        });

        // All CHURCH giving types for editing selection
        $allChurchTypes = GivingType::query()
            ->where('contribution_type', ContributionType::CHURCH->value)
            ->orderBy('name')
            ->get(['id', 'name', 'key', 'auto_assignable']);

        return Inertia::render('Branches/Givings', [
            'branch' => [
                'id' => $branch->id,
                'name' => $branch->name,
            ],
            'givingTypes' => $typesPayload,
            'allGivingTypes' => $allChurchTypes,
            'assignedGivingTypeIds' => $branch->givingTypes->pluck('id')->values(),
        ]);
    }

    /**
     * Update the systems attached to a branch for a specific giving type.
     *
     * @param Branch $branch
     * @param GivingType $givingType
     * @param UpdateBranchGivingTypeSystemsData $data
     */
    public function updateSystems(Branch $branch, GivingType $givingType, UpdateBranchGivingTypeSystemsData $data): RedirectResponse
    {
        // Only allow syncing systems that belong to this giving type and are assignable.
        $allowedIds = GivingTypeSystem::query()
            ->where('giving_type_id', $givingType->id)
            ->where('assignable', true)
            ->pluck('id')
            ->all();

        $desired = collect($data->system_ids ?? [])
            ->map(static fn ($v) => (int) $v)
            ->intersect($allowedIds)
            ->values()
            ->all();

        $current = $branch->givingTypeSystems()
            ->where('giving_type_id', $givingType->id)
            ->pluck('giving_type_systems.id')
            ->all();

        $toAttach = array_values(array_diff($desired, $current));
        $toDetach = array_values(array_diff($current, $desired));

        if ($toAttach !== []) {
            $branch->givingTypeSystems()->syncWithoutDetaching($toAttach);
        }
        if ($toDetach !== []) {
            $branch->givingTypeSystems()->detach($toDetach);
        }

        return redirect()->route('branches.givings', ['branch' => $branch->id])
            ->with('success', 'Updated branch giving type systems.');
    }

    /**
     * Update assigned giving types for a branch (CHURCH only) and reconcile systems.
     *
     * @param Branch $branch
     * @param UpdateMemberGivingTypesData $data
     */
    public function updateGivingTypes(Branch $branch, UpdateMemberGivingTypesData $data): RedirectResponse
    {
        $allowedGivingTypeIds = GivingType::query()
            ->where('contribution_type', ContributionType::CHURCH->value)
            ->pluck('id')
            ->all();

        $desired = collect($data->giving_type_ids ?? [])
            ->map(static fn ($v) => (int) $v)
            ->intersect($allowedGivingTypeIds)
            ->values()
            ->all();

        $branch->givingTypes()->sync($desired);

        if ($desired !== []) {
            $autoSystemIds = GivingTypeSystem::query()
                ->whereIn('giving_type_id', $desired)
                ->where('assignable', true)
                ->where('auto_assignable', true)
                ->pluck('id')
                ->all();

            if ($autoSystemIds !== []) {
                $branch->givingTypeSystems()->syncWithoutDetaching($autoSystemIds);
            }
        }

        if ($desired === []) {
            $branch->givingTypeSystems()->detach();
        } else {
            $systemIdsToDetach = $branch->givingTypeSystems()
                ->whereNotIn('giving_type_id', $desired)
                ->pluck('giving_type_systems.id')
                ->all();
            if ($systemIdsToDetach !== []) {
                $branch->givingTypeSystems()->detach($systemIdsToDetach);
            }
        }

        return redirect()->route('branches.givings', ['branch' => $branch->id])
            ->with('success', 'Updated branch giving types.');
    }
}
