<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreateMemberData;
use App\Data\UpdateMemberData;
use App\Models\Member;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberRepository
{
    public function __construct(
        protected TagRepository $tagRepository,
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
        return Member::with($relations)->orderBy('first_name')->paginate();
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

        $tagIds = $this->tagRepository->getIdsForKeys($data->tags, createMissing: true);
        if ($tagIds !== []) {
            $member->tags()->sync($tagIds);
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

        $tagIds = $this->tagRepository->getIdsForKeys($data->tags, createMissing: true);
        $member->tags()->sync($tagIds);

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
