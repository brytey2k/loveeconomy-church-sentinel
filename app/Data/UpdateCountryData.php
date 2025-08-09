<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Country;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdateCountryData extends Data
{
    public function __construct(
        #[FromRouteParameter('country')]
        public Country $country,
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
                Rule::unique(Country::class, 'name')
                    ->withoutTrashed()
                    ->ignore($context?->payload['country']->id ?? 0)
            ],
        ];
    }
}
