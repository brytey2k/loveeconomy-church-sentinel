<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\GivingType;
use App\Models\GivingTypeSystem;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateGivingTypeSystemData extends Data
{
    public function __construct(
        public int $giving_type_id,
        public int|null $parent_id,
        public string $name,
        public float|null $amount_low = null,
        public float|null $amount_high = null,
        public bool $assignable = true,
        public bool $auto_assignable = false,
    ) {
    }

    /**
     * @param GivingTypeSystem $givingTypeSystem
     *
     * @return array<string, mixed>
     */
    public static function rules(GivingTypeSystem $givingTypeSystem): array
    {
        return [
            'giving_type_id' => ['required', 'integer', Rule::exists(GivingType::class, 'id')],
            'parent_id' => ['nullable', 'integer', Rule::exists(GivingTypeSystem::class, 'id')],
            'name' => ['required', 'string', 'max:255'],
            'assignable' => ['required', 'boolean'],
            'auto_assignable' => [
                'required',
                'boolean',
                static function (string $attribute, mixed $value, callable $fail) {
                    $assignable = (bool) request()->input('assignable', true);
                    if ($assignable === false && (bool) $value === true) {
                        $fail('Auto assignable can only be set when assignable is Yes.');
                    }
                },
            ],
            'amount_low' => ['nullable', 'numeric', 'required_if:assignable,1,true', 'gt:0'],
            'amount_high' => ['nullable', 'numeric', 'required_if:assignable,1,true', 'gte:amount_low', 'gt:0'],
        ];
    }
}
