<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreatePositionData;
use App\Data\UpdatePositionData;
use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Repositories\PositionsRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PositionsController extends Controller
{
    public function __construct(
        protected PositionsRepository $positionsRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Positions/Index', [
            'positions' => $this->positionsRepository->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Positions/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePositionData $positionData
     */
    public function store(CreatePositionData $positionData): RedirectResponse
    {
        $this->positionsRepository->create($positionData);

        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Position $position
     */
    public function edit(Position $position): Response
    {
        return Inertia::render('Positions/Edit', [
            'position' => $position,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePositionData $updatePositionData
     * @param Position $position
     */
    public function update(UpdatePositionData $updatePositionData, Position $position): RedirectResponse
    {
        $this->positionsRepository->update($position, $updatePositionData);

        return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Position $position
     */
    public function destroy(Position $position): RedirectResponse
    {
        $this->positionsRepository->delete($position);

        return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
    }
}
