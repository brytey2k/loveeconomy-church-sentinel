<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Level;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdateLevelData extends Data
{
    public function __construct(
        #[FromRouteParameter('level')]
        public Level $level,
        public string $name,
        public int $position,
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
            'name' => ['required', 'string', 'max:255'],
            'position' => [
                'required',
                'integer',
                Rule::unique(Level::class, 'position')
                    ->ignore($context?->payload['level']->id ?? 0)
                    ->withoutTrashed()
            ],
        ];
    }
}
