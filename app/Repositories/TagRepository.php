<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @param bool $onlyTrashed
     *
     * @return LengthAwarePaginator<Tag>
     */
    public function paginate(bool $onlyTrashed = false): LengthAwarePaginator
    {
        $query = Tag::query()->orderBy('name');
        if ($onlyTrashed) {
            $query->onlyTrashed();
        }
        return $query->paginate();
    }

    public function create(array $attributes): Tag
    {
        return Tag::query()->create($attributes);
    }

    public function update(Tag $tag, array $attributes): Tag
    {
        $tag->update($attributes);
        return $tag;
    }

    public function delete(Tag $tag): bool
    {
        return (bool) $tag->delete();
    }

    public function restore(Tag $tag): bool
    {
        return (bool) $tag->restore();
    }

    /**
     * Resolve IDs for given keys, creating missing tags if requested.
     *
     * @param array<int, string> $keys
     * @param bool $createMissing
     *
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
