<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateCurrencyData extends Data
{
    public function __construct(
        public string $name,
        public string $short_name,
        public string $symbol,
    ) {
    }

    /**
     * @return array<string, list<string>>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['required', 'string', 'max:10'],
            'symbol' => ['required', 'string', 'max:10'],
        ];
    }
}
