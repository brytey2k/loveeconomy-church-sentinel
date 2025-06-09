<?php

declare(strict_types=1);

namespace App\Http\Controllers\Structure;

use App\Http\Controllers\Controller;
use App\Http\Requests\Structure\Branches\CreateBranchRequest;
use App\Http\Requests\Structure\Branches\UpdateBranchRequest;
use App\Http\Responses\SuccessResponse;
use App\Models\Branch;
use App\Repositories\Structure\BranchesRepository;
use Symfony\Component\HttpFoundation\Response;

class BranchesController extends Controller
{
    public function __construct(protected BranchesRepository $branchesRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SuccessResponse::make(
            data: $this->branchesRepository->list()
        );
    }

    public function store(CreateBranchRequest $request)
    {
        return SuccessResponse::make(
            data: $this->branchesRepository->create($request->toDto())->toArray(),
            statusCode: Response::HTTP_CREATED
        );
    }

    public function show(Branch $branch)
    {
        return SuccessResponse::make(
            data: $branch->toArray(),
        );
    }

    public function update(UpdateBranchRequest $request, Branch $branch)
    {
        return SuccessResponse::make(
            data: $this->branchesRepository->update($branch, $request->toDto())->toArray(),
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Branch $branch
     *
     * @throws \Throwable
     *
     * @return SuccessResponse
     */
    public function destroy(Branch $branch)
    {
        return SuccessResponse::make(
            data: $this->branchesRepository->delete($branch)->toArray()
        );
    }
}
