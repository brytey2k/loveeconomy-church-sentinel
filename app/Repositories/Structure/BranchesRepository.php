<?php

declare(strict_types=1);

namespace App\Repositories\Structure;

use App\Dto\Structure\Branches\CreateBranchDto;
use App\Dto\Structure\Branches\UpdateBranchDto;
use App\Models\Branch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Umbrellio\LTree\Interfaces\LTreeServiceInterface;

class BranchesRepository
{
    public function __construct(protected LTreeServiceInterface $lTreeService)
    {
    }

    /**
     * @return LengthAwarePaginator<Branch>
     */
    public function list(): LengthAwarePaginator
    {
        return Branch::paginate();
    }

    /**
     * @param CreateBranchDto $createBranchDto
     *
     * @throws \Throwable
     */
    public function create(CreateBranchDto $createBranchDto): Branch
    {
        return DB::transaction(function () use ($createBranchDto) {
            $branch = Branch::create($createBranchDto->toArray());
            $this->lTreeService->createPath($branch);

            return $branch;
        });
    }

    /**
     * @param Branch $branch
     * @param UpdateBranchDto $updateBranchDto
     *
     * @throws \Throwable
     */
    public function update(Branch $branch, UpdateBranchDto $updateBranchDto): Branch
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
        return DB::transaction(static function () use ($branch) {
            // todo: contribute this to ltree package

            // todo: we need to correct the parent_id fields of the children
            DB::statement(sprintf(
                "update %s set %s = subpath(%s, 1) where %s <@ '%s' and %s != '%s'",
                $branch->getTable(),
                $branch->getLtreePathColumn(),
                $branch->getLtreePathColumn(),
                $branch->getLtreePathColumn(),
                $branch->{$branch->getLtreePathColumn()},
                $branch->getLtreePathColumn(),
                $branch->{$branch->getLtreePathColumn()},
            ));

            $branch->delete();

            return $branch;
        });
    }
}
