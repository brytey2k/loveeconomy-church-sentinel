<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Branch;
use App\Models\Position;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateMemberData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $phone,
        public int $branch_id,
        public int $position_id,
        /**
         * Array of tag keys to assign to the member, e.g., ['partner','tither']
         * @var array<int, string>
         */
        public array $tags = [],
    ) {
    }

    /**
     * @return array<string, list<string>>
     */
    public static function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'branch_id' => ['required', 'integer', Rule::exists(Branch::class, 'id')],
            'position_id' => ['required', 'integer', Rule::exists(Position::class, 'id')],
            'tags' => ['array'],
            'tags.*' => ['string', 'max:100'],
        ];
    }
}
