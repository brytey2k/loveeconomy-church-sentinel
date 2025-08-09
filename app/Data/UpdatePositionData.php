<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Position;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdatePositionData extends Data
{
    public function __construct(
        #[FromRouteParameter('position')]
        public Position $position,
        public string $name,
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Position::class, 'name')
                    ->withoutTrashed()
                    ->ignore($context?->payload['position']->id ?? 0)
            ],
        ];
    }
}
