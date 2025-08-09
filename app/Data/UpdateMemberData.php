<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Branch;
use App\Models\Member;
use App\Models\Position;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdateMemberData extends Data
{
    public function __construct(
        #[FromRouteParameter('member')]
        public Member $member,
        public string $first_name,
        public string $last_name,
        public string $phone,
        public int $branch_id,
        public int $position_id,
        /**
         * Array of tag keys to assign to the member
         * @var array<int, string>
         */
        public array $tags = [],
    ) {
    }

    /**
     * @param ValidationContext|null $context
     *
     * @return array<string, list<\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(ValidationContext|null $context): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique(Member::class, 'phone')
                    ->withoutTrashed()
                    ->ignore($context?->payload['member']->id ?? 0)
            ],
            'branch_id' => [
                'required',
                'integer',
                Rule::exists(Branch::class, 'id'),
            ],
            'position_id' => [
                'required',
                'integer',
                Rule::exists(Position::class, 'id'),
            ],
            'tags' => [
                'array',
            ],
            'tags.*' => [
                'string', 'max:100'
            ],
        ];
    }
}
