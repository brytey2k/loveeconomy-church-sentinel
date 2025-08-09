<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Currency;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UpdateCurrencyData extends Data
{
    public function __construct(
        #[FromRouteParameter('currency')]
        public Currency $currency,
        public string $name,
        public string $short_name,
        public string $symbol,
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
                Rule::unique(Currency::class, 'name')
                    ->withoutTrashed()
                    ->ignore($context?->payload['currency']->id ?? 0)
            ],
            'short_name' => [
                'required',
                'string',
                'max:10',
                Rule::unique(Currency::class, 'short_name')
                    ->withoutTrashed()
                    ->ignore($context?->payload['currency']->id ?? 0)
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
            ],
        ];
    }
}
