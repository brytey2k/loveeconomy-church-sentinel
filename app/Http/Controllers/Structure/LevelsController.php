<?php

declare(strict_types=1);

namespace App\Http\Controllers\Structure;

use App\Http\Controllers\Controller;
use App\Http\Requests\Structure\Levels\CreateLevelRequest;
use App\Http\Requests\Structure\Levels\UpdateLevelRequest;
use App\Http\Responses\SuccessResponse;
use App\Models\Level;
use App\Repositories\Structure\LevelsRepository;
use Symfony\Component\HttpFoundation\Response;

class LevelsController extends Controller
{
    public function __construct(protected LevelsRepository $levelsRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): SuccessResponse
    {
        return SuccessResponse::make(
            data: $this->levelsRepository->get()->toArray()
        );
    }

    public function store(CreateLevelRequest $request): SuccessResponse
    {
        return SuccessResponse::make(
            data: $this->levelsRepository->create($request->toDto())->toArray(),
            statusCode: Response::HTTP_CREATED
        );
    }

    public function show(Level $level): SuccessResponse
    {
        return SuccessResponse::make(
            data: $level->toArray(),
        );
    }

    public function update(UpdateLevelRequest $request, Level $level): SuccessResponse
    {
        return SuccessResponse::make(
            data: $this->levelsRepository->update($level, $request->toDto())->toArray(),
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Level $level
     */
    public function destroy(Level $level): SuccessResponse
    {
        return SuccessResponse::make(
            data: $this->levelsRepository->delete($level)->toArray()
        );
    }
}
