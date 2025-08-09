<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Position;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CreatePositionData extends Data
{
    public function __construct(
        public string $name,
    ) {
    }

    /**
     * @return array<string, list<\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Position::class, 'name')->withoutTrashed()],
        ];
    }
}
