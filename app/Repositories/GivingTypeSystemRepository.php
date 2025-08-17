<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreateGivingTypeSystemData;
use App\Data\UpdateGivingTypeSystemData;
use App\Models\GivingTypeSystem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Umbrellio\LTree\Interfaces\LTreeServiceInterface;

class GivingTypeSystemRepository
{
    public function __construct(protected LTreeServiceInterface $lTreeService)
    {
    }

    /**
     * @param int|null $givingTypeId
     *
     * @return LengthAwarePaginator<GivingTypeSystem>
     */
    public function paginate(int|null $givingTypeId = null): LengthAwarePaginator
    {
        $query = GivingTypeSystem::query()
            ->with(['givingType', 'parent'])
            ->orderBy('name');

        if ($givingTypeId !== null) {
            $query->where('giving_type_id', $givingTypeId);
        }

        return $query->paginate();
    }

    public function allForType(int $givingTypeId)
    {
        return GivingTypeSystem::query()
            ->where('giving_type_id', $givingTypeId)
            ->orderBy('name')
            ->get();
    }

    public function create(CreateGivingTypeSystemData $dto): GivingTypeSystem
    {
        $attributes = $this->normalizeAmounts($dto->toArray());
        // Ensure NOT NULL for path on insert (similar to Branches flow)
        $attributes['path'] ??= '';

        return DB::transaction(function () use ($attributes) {
            // Create first with an empty path, then let LTree set it properly
            $model = GivingTypeSystem::query()->create($attributes);
            // Let Umbrellio LTree set the path
            $this->lTreeService->createPath($model);

            return $model;
        });
    }

    public function update(GivingTypeSystem $model, UpdateGivingTypeSystemData $dto): GivingTypeSystem
    {
        return DB::transaction(function () use ($model, $dto) {
            $normalized = $this->normalizeAmounts(array_merge($model->toArray(), $dto->toArray()));
            $model->update($normalized);
            // Use LTree service to update the path (similar to BranchesRepository)
            $this->lTreeService->updatePath($model);

            return $model;
        });
    }

    public function delete(GivingTypeSystem $model): bool
    {
        // Minimal behavior unchanged; if needed later, we can also drop descendants here
        return (bool) $model->delete();
    }

    /**
     * When assignable is false, force amounts to 0. Always ensure numeric values.
     *
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    private function normalizeAmounts(array $attributes): array
    {
        $assignable = (bool) ($attributes['assignable'] ?? true);
        if (!$assignable) {
            $attributes['amount_low'] = 0;
            $attributes['amount_high'] = 0;
            // When not assignable, auto-assignable must be false
            $attributes['auto_assignable'] = false;
        } else {
            $attributes['amount_low'] = isset($attributes['amount_low']) ? (float) $attributes['amount_low'] : 0;
            $attributes['amount_high'] = isset($attributes['amount_high']) ? (float) $attributes['amount_high'] : 0;
            // Ensure boolean cast shape for auto_assignable too
            if (isset($attributes['auto_assignable'])) {
                $attributes['auto_assignable'] = (bool) $attributes['auto_assignable'];
            }
        }
        return $attributes;
    }
}
