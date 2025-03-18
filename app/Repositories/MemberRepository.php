<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\CreateMemberDto;
use App\Models\Member;

class MemberRepository
{
    public function findByPhoneNumber(string $phoneNumber): Member|null
    {
        return Member::query()->where('phone', $phoneNumber)->first();
    }

    public function create(CreateMemberDto $data): Member
    {
        return Member::query()->create($data->toArray());
    }
}
