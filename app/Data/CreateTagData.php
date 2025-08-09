<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Tag;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CreateTagData extends Data
{
    public function __construct(
        public string $key,
        public string $name,
        public string|null $description,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:100', Rule::unique(Tag::class, 'key')->withoutTrashed()],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
