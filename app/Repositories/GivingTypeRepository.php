<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GivingType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GivingTypeRepository
{
    /**
     * @return Collection<int, GivingType>
     */
    public function all(): Collection
    {
        return GivingType::query()->orderBy('name')->get();
    }

    /**
     * @param bool $onlyTrashed
     *
     * @return LengthAwarePaginator<GivingType>
     */
    public function paginate(bool $onlyTrashed = false): LengthAwarePaginator
    {
        $query = GivingType::query()->orderBy('name');
        if ($onlyTrashed) {
            $query->onlyTrashed();
        }
        return $query->paginate();
    }

    public function create(array $attributes): GivingType
    {
        return GivingType::query()->create($attributes);
    }

    public function update(GivingType $tag, array $attributes): GivingType
    {
        $tag->update($attributes);
        return $tag;
    }

    public function delete(GivingType $tag): bool
    {
        return (bool) $tag->delete();
    }

    public function restore(GivingType $tag): bool
    {
        return (bool) $tag->restore();
    }

    /**
     * Resolve IDs for given keys, creating missing giving types if requested.
     *
     * @param array<int, string> $keys
     * @param bool $createMissing
     *
     * @return array<int, int> giving_type IDs
     */
    public function getIdsForKeys(array $keys, bool $createMissing = true): array
    {
        $keys = array_values(array_unique(array_filter($keys)));
        if ($keys === []) {
            return [];
        }

        $existing = GivingType::query()->whereIn('key', $keys)->get(['id', 'key']);
        $map = [];
        foreach ($existing as $gt) {
            $map[$gt->key] = $gt->id;
        }

        if ($createMissing) {
            $missing = array_values(array_diff($keys, array_keys($map)));
            foreach ($missing as $key) {
                $gt = GivingType::query()->create([
                    'key' => $key,
                    'name' => ucfirst(str_replace(['_', '-'], ' ', $key)),
                ]);
                    $map[$key] = $gt->id;
            }
        }

        return array_values($map);
    }
}
