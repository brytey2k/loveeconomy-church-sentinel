<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\UpdateMemberGivingTypeSystemsData;
use App\Enums\ContributionType;
use App\Http\Controllers\Controller;
use App\Models\GivingType;
use App\Models\GivingTypeSystem;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MemberGivingsController extends Controller
{
    /**
     * Show a page to manage a member's assigned giving types and systems.
     *
     * @param Member $member
     */
    public function show(Member $member): Response
    {
        // Eager-load the member's giving types and currently attached systems
        $member->load(['givingTypes', 'givingTypeSystems']);

        // Build a structure per giving type:
        // - all assignable systems for the type
        // - currently attached system IDs for the member
        $givingTypes = GivingType::query()
            ->where('contribution_type', ContributionType::INDIVIDUAL->value)
            ->whereIn('id', $member->givingTypes->pluck('id')->all())
            ->orderBy('name')
            ->get();

        $typesPayload = $givingTypes->map(static function (GivingType $type) use ($member) {
            // All systems for this type that are assignable
            $systems = GivingTypeSystem::query()
                ->where('giving_type_id', $type->id)
                ->where('assignable', true)
                ->orderBy('name')
                ->get(['id', 'name', 'amount_low', 'amount_high', 'auto_assignable']);

            // Currently attached system IDs of this type
            $attachedIds = $member->givingTypeSystems
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

        return Inertia::render('Members/Givings', [
            'member' => [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'phone' => $member->phone,
            ],
            'givingTypes' => $typesPayload,
        ]);
    }

    /**
     * Update the systems attached to a member for a specific giving type.
     *
     * @param Member $member
     * @param GivingType $givingType
     * @param UpdateMemberGivingTypeSystemsData $data
     */
    public function updateSystems(Member $member, GivingType $givingType, UpdateMemberGivingTypeSystemsData $data): RedirectResponse
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

        // Current attached systems for this member limited to this type
        $current = $member->givingTypeSystems()
            ->where('giving_type_id', $givingType->id)
            ->pluck('giving_type_systems.id')
            ->all();

        // Determine changes
        $toAttach = array_values(array_diff($desired, $current));
        $toDetach = array_values(array_diff($current, $desired));

        if ($toAttach !== []) {
            $member->givingTypeSystems()->syncWithoutDetaching($toAttach);
        }
        if ($toDetach !== []) {
            $member->givingTypeSystems()->detach($toDetach);
        }

        return redirect()->route('members.givings', ['member' => $member->id])
            ->with('success', 'Updated giving type systems.');
    }
}
