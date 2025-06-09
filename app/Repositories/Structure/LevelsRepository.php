<?php

declare(strict_types=1);

namespace App\Repositories\Structure;

use App\Dto\Structure\Levels\CreateLevelDto;
use App\Dto\Structure\Levels\UpdateLevelDto;
use App\Exceptions\LevelPositionAlreadyExistsException;
use App\Models\Level;
use Illuminate\Database\Eloquent\Collection;

class LevelsRepository
{
    public function create(CreateLevelDto $createLevelDto): Level
    {
        return Level::create($createLevelDto->toArray());
    }

    public function get(): Collection
    {
        return Level::get();
    }

    public function positionExists(int $position): bool
    {
        return Level::where('position', $position)->exists();
    }

    /**
     * @param Level $level
     * @param UpdateLevelDto $updateLevelDto
     *
     * @throws LevelPositionAlreadyExistsException
     */
    public function update(Level $level, UpdateLevelDto $updateLevelDto): Level
    {
        if ($updateLevelDto->position !== $level->position && $this->positionExists($updateLevelDto->position)) {
            throw new LevelPositionAlreadyExistsException('Position already exists');
        }

        $level->update($updateLevelDto->toArray());

        return $level;
    }

    public function delete(Level $level): Level
    {
        $level->delete();

        return $level;
    }
}
