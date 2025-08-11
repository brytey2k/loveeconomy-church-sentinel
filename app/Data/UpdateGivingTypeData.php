<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\GivingType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateGivingTypeData extends Data
{
    public function __construct(
        public string $key,
        public string $name,
        public string|null $description,
        public string $contribution_type = 'individual',
    ) {
    }

    /**
     * @param GivingType $givingType
     *
     * @return array<string, mixed>
     */
    public static function rules(GivingType $givingType): array
    {
        return [
            'key' => ['required', 'string', 'max:100', Rule::unique(GivingType::class, 'key')->ignore($givingType->id)->withoutTrashed()],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contribution_type' => ['required', 'string'],
        ];
    }
}
