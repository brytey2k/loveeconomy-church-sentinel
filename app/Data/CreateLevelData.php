<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Level;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateLevelData extends Data
{
    public function __construct(
        public string $name,
        public int $position,
    ) {
    }

    /**
     * @return array<string, list<\Illuminate\Validation\Rules\Unique|string>>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'integer', Rule::unique(Level::class, 'position')->withoutTrashed()],
        ];
    }
}
