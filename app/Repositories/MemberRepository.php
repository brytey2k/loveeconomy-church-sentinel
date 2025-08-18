<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreateMemberData;
use App\Data\UpdateMemberData;
use App\Enums\ContributionType;
use App\Models\GivingType;
use App\Models\GivingTypeSystem;
use App\Models\Member;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberRepository
{
    public function __construct(
        protected GivingTypeRepository $tagRepository,
    ) {
    }

    /**
     * Get all members without pagination, ordered by first_name.
     *
     * @param array<string> $relations The relations to eager load
     *
     * @return Collection<int, Member>
     */
    public function all(array $relations = []): Collection
    {
        return Member::with($relations)->orderBy('first_name')->get();
    }

    /**
     * Get all members with pagination, ordered by first_name.
     *
     * @param array<string> $relations The relations to eager load
     *
     * @return LengthAwarePaginator<Member>
     */
    public function paginate(array $relations = []): LengthAwarePaginator
    {
        return Member::with($relations)
            ->orderBy('first_name')
            ->paginate()
            ->withQueryString();
    }

    /**
     * Paginate members filtered by a specific Giving Type.
     *
     * @param ?int $givingTypeId
     * @param ?string $givingTypeKey
     * @param array<string> $relations
     */
    public function paginateByGivingType(int|null $givingTypeId, string|null $givingTypeKey = null, array $relations = []): LengthAwarePaginator
    {
        $query = Member::with($relations)
            ->whereHas('givingTypes', static function ($q) use ($givingTypeId, $givingTypeKey) {
                if ($givingTypeId !== null) {
                    $q->whereKey($givingTypeId);
                } elseif ($givingTypeKey !== null && $givingTypeKey !== '') {
                    $q->where('key', $givingTypeKey);
                }
            })
            ->orderBy('first_name');

        return $query->paginate()->withQueryString();
    }

    /**
     * Get a single member by ID.
     *
     * @param int $id The ID of the member to find
     *
     * @return Member|null The member if found, null otherwise
     */
    public function find(int $id): Member|null
    {
        return Member::find($id);
    }

    public function findByPhoneNumber(string $phoneNumber): Member|null
    {
        return Member::query()->where('phone', $phoneNumber)->first();
    }

    /**
     * Create a new member with tags.
     *
     * @param CreateMemberData $data
     */
    public function create(CreateMemberData $data): Member
    {
        $payload = [
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'phone' => $data->phone,
            'branch_id' => $data->branch_id,
            'position_id' => $data->position_id,
        ];

        $member = Member::query()->create($payload);

        // 1) Resolve IDs for incoming giving type keys (defensive cast to array)
        $incomingKeys = is_array($data->tags) ? $data->tags : (($data->tags !== null && $data->tags !== '') ? [$data->tags] : []);
        $tagIds = $this->tagRepository->getIdsForKeys($incomingKeys, createMissing: true);

        // Keep only INDIVIDUAL contribution type for any user-selected giving types
        if ($tagIds !== []) {
            $tagIds = GivingType::query()
                ->whereIn('id', $tagIds)
                ->where('contribution_type', ContributionType::INDIVIDUAL->value)
                ->pluck('id')
                ->all();
        }

        // 2) Merge in auto-assignable giving types (server-side enforcement), only INDIVIDUAL
        $autoAssignableIds = GivingType::query()
            ->where('auto_assignable', true)
            ->where('contribution_type', ContributionType::INDIVIDUAL->value)
            ->pluck('id')
            ->all();

        $mergedTagIds = array_values(array_unique(array_merge($tagIds, $autoAssignableIds)));

        if ($mergedTagIds !== []) {
            // Use sync to ensure current state; we included user selection + auto-assignables
            $member->givingTypes()->sync($mergedTagIds);
        }

        // 3) Attach auto-assignable Giving Type Systems for ALL assigned INDIVIDUAL giving types
        if ($mergedTagIds !== []) {
            $systemIds = GivingTypeSystem::query()
                ->whereIn('giving_type_id', $mergedTagIds)
                ->where('assignable', true)
                ->where('auto_assignable', true)
                ->pluck('id')
                ->all();

            if ($systemIds !== []) {
                $member->givingTypeSystems()->syncWithoutDetaching($systemIds);
            }
        }

        return $member;
    }

    /**
     * Update an existing member and its tags.
     *
     * @param Member $member
     * @param UpdateMemberData $data
     */
    public function update(Member $member, UpdateMemberData $data): Member
    {
        $payload = [
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'phone' => $data->phone,
            'branch_id' => $data->branch_id,
            'position_id' => $data->position_id,
        ];
        $member->update($payload);

        // 1) Resolve IDs for incoming giving type keys (defensive cast to array)
        $incomingKeys = is_array($data->tags) ? $data->tags : (($data->tags !== null && $data->tags !== '') ? [$data->tags] : []);
        $tagIds = $this->tagRepository->getIdsForKeys($incomingKeys, createMissing: true);

        // 2) Merge in auto-assignable giving types
        $autoAssignableIds = GivingType::query()
            ->where('auto_assignable', true)
            ->pluck('id')
            ->all();

        $mergedTagIds = array_values(array_unique(array_merge($tagIds, $autoAssignableIds)));

        $member->tags()->sync($mergedTagIds);

        // 3) Ensure auto-assignable systems are attached
        if ($autoAssignableIds !== []) {
            $systemIds = GivingTypeSystem::query()
                ->whereIn('giving_type_id', $autoAssignableIds)
                ->where('assignable', true)
                ->where('auto_assignable', true)
                ->pluck('id')
                ->all();

            if ($systemIds !== []) {
                $member->givingTypeSystems()->syncWithoutDetaching($systemIds);
            }
        }

        return $member;
    }

    /**
     * Delete a member.
     *
     * @param Member $member
     */
    public function delete(Member $member): bool
    {
        return $member->delete();
    }
}
