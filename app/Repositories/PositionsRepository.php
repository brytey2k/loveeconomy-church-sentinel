<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreatePositionData;
use App\Data\UpdatePositionData;
use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PositionsRepository
{
    /**
     * Get all positions without pagination, ordered by name.
     *
     * @return Collection<int, Position>
     */
    public function all(): Collection
    {
        return Position::orderBy('name')->get();
    }

    /**
     * Get all positions with pagination, ordered by name.
     *
     * @return LengthAwarePaginator<Position>
     */
    public function paginate(): LengthAwarePaginator
    {
        return Position::orderBy('name')->paginate();
    }

    /**
     * Get a single position by ID.
     *
     * @param int $id The ID of the position to find
     *
     * @return Position|null The position if found, null otherwise
     */
    public function find(int $id): Position|null
    {
        return Position::find($id);
    }

    /**
     * Create a new position.
     *
     * @param CreatePositionData $data The data for creating a new position
     *
     * @return Position The newly created position
     */
    public function create(CreatePositionData $data): Position
    {
        return Position::query()->create($data->toArray());
    }

    /**
     * Update an existing position.
     *
     * @param Position $position The position to update
     * @param UpdatePositionData $data The data for updating the position
     *
     * @return Position The updated position
     */
    public function update(Position $position, UpdatePositionData $data): Position
    {
        $position->update($data->toArray());

        return $position;
    }

    /**
     * Delete a position.
     *
     * @param Position $position The position to delete
     *
     * @return bool True if the position was deleted successfully, false otherwise
     */
    public function delete(Position $position): bool
    {
        return $position->delete();
    }
}
