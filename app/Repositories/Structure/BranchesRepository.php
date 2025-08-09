<?php

declare(strict_types=1);

namespace App\Repositories\Structure;

use App\Data\CreateBranchData;
use App\Data\UpdateBranchData;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Umbrellio\LTree\Interfaces\LTreeServiceInterface;

class BranchesRepository
{
    public function __construct(protected LTreeServiceInterface $lTreeService)
    {
    }

    /**
     * @param array $relations
     *
     * @return LengthAwarePaginator<Branch>
     */
    public function list(array $relations = []): LengthAwarePaginator
    {
        return Branch::with($relations)->paginate();
    }

    public function all(): Collection
    {
        return Branch::all();
    }

    public function allExcept(array $ids): Collection
    {
        return Branch::whereNotIn('id', $ids)->get();
    }

    /**
     * @param CreateBranchData $createBranchData
     *
     * @throws \Throwable
     */
    public function create(CreateBranchData $createBranchData): Branch
    {
        return DB::transaction(function () use ($createBranchData) {
            $branch = Branch::create($createBranchData->toArray());
            $this->lTreeService->createPath($branch);

            return $branch;
        });
    }

    /**
     * @param Branch $branch
     * @param UpdateBranchData $updateBranchDto
     *
     * @throws \Throwable
     */
    public function update(Branch $branch, UpdateBranchData $updateBranchDto): Branch
    {
        return DB::transaction(function () use ($branch, $updateBranchDto) {
            $branch->update($updateBranchDto->toArray());
            $this->lTreeService->updatePath($branch);

            return $branch;
        });
    }

    /**
     * @param Branch $branch
     *
     * @throws \Throwable
     */
    public function delete(Branch $branch): Branch
    {
        return DB::transaction(function () use ($branch) {
            // Get the parent ID of the branch being deleted
            $parentId = $branch->parent_id;

            // Update the parent_id of direct children to the parent of the deleted branch
            Branch::where('parent_id', $branch->id)
                ->update(['parent_id' => $parentId]);

            // Drop descendants using the LTreeService
            $this->lTreeService->dropDescendants($branch);

            $branch->delete();

            return $branch;
        });
    }

    public function find(int $id): Branch|null
    {
        return Branch::find($id);
    }
}
