<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreateLevelData;
use App\Data\UpdateLevelData;
use App\Models\Level;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LevelRepository
{
    /**
     * @return LengthAwarePaginator<Level>
     */
    public function paginate(): LengthAwarePaginator
    {
        return Level::orderBy('position')->paginate();
    }

    /**
     * @return Collection<int, Level>
     */
    public function all(): Collection
    {
        return Level::orderBy('position')->get();
    }

    public function create(CreateLevelData $levelData): Level
    {
        return Level::create($levelData->toArray());
    }

    public function update(UpdateLevelData $levelData, Level $level): Level
    {
        $level->update([
            'name' => $levelData->name,
            'position' => $levelData->position,
        ]);

        return $level;
    }

    public function delete(Level $level): bool
    {
        return $level->delete();
    }
}
