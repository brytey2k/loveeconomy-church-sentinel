<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Branch;
use App\Models\Country;
use App\Models\Level;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateBranchData extends Data
{
    public function __construct(
        public string $name,
        public int $level_id,
        public int $country_id,
        public string $currency,
        public int|null $parent_id = null,
    ) {
    }

    /**
     * @return array<string, list<\Illuminate\Validation\Rules\Exists|string>>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'level_id' => ['required', 'integer', Rule::exists(Level::class, 'id')],
            'country_id' => ['required', 'integer', Rule::exists(Country::class, 'id')],
            'parent_id' => ['nullable', 'integer', Rule::exists(Branch::class, 'id')],
            'currency' => ['required', 'string', 'size:3', Rule::exists(\App\Models\Currency::class, 'short_name')],
        ];
    }
}
