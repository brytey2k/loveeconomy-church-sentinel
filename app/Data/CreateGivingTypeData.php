<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\GivingType;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateGivingTypeData extends Data
{
    public function __construct(
        public string $key,
        public string $name,
        public string|null $description,
        public string $contribution_type = 'individual',
        public bool $auto_assignable = false,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:100', Rule::unique(GivingType::class, 'key')->withoutTrashed()],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contribution_type' => ['required', 'string'],
            'auto_assignable' => ['required', 'boolean'],
        ];
    }
}
