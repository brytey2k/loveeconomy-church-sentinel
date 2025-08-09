<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagRepository
{
    /**
     * @return Collection<int, Tag>
     */
    public function all(): Collection
    {
        return Tag::query()->orderBy('name')->get();
    }

    /**
     * Resolve IDs for given keys, creating missing tags if requested.
     *
     * @param array<int, string> $keys
     * @param bool $createMissing
     * @return array<int, int> tag IDs
     */
    public function getIdsForKeys(array $keys, bool $createMissing = true): array
    {
        $keys = array_values(array_unique(array_filter($keys)));
        if ($keys === []) {
            return [];
        }

        $existing = Tag::query()->whereIn('key', $keys)->get(['id', 'key']);
        $map = [];
        foreach ($existing as $tag) {
            $map[$tag->key] = $tag->id;
        }

        if ($createMissing) {
            $missing = array_values(array_diff($keys, array_keys($map)));
            foreach ($missing as $key) {
                $tag = Tag::query()->create([
                    'key' => $key,
                    'name' => ucfirst(str_replace(['_', '-'], ' ', $key)),
                ]);
                $map[$key] = $tag->id;
            }
        }

        return array_values($map);
    }
}
