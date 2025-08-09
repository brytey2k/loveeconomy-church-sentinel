<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Tag;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateTagData extends Data
{
    public function __construct(
        public string $key,
        public string $name,
        public string|null $description,
    ) {
    }

    /**
     * @param Tag $tag
     *
     * @return array<string, mixed>
     */
    public static function rules(Tag $tag): array
    {
        return [
            'key' => ['required', 'string', 'max:100', Rule::unique(Tag::class, 'key')->ignore($tag->id)->withoutTrashed()],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
