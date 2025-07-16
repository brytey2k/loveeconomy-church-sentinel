<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateLevelData;
use App\Data\UpdateLevelData;
use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Repositories\LevelRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LevelsController extends Controller
{
    public function __construct(protected LevelRepository $levelRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Levels/Index', [
            'levels' => $this->levelRepository->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Levels/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateLevelData $levelData
     */
    public function store(CreateLevelData $levelData): RedirectResponse
    {
        $this->levelRepository->create($levelData);

        return redirect()->route('levels.index')->with('success', 'Level created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Level $level
     */
    public function edit(Level $level): Response
    {
        return Inertia::render('Levels/Edit', [
            'level' => $level,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLevelData $updateLevelData
     * @param Level $level
     */
    public function update(UpdateLevelData $updateLevelData, Level $level): RedirectResponse
    {
        $this->levelRepository->update($updateLevelData, $level);

        return redirect()->route('levels.index')->with('success', 'Level updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Level $level
     */
    public function destroy(Level $level): RedirectResponse
    {
        $this->levelRepository->delete($level);

        return redirect()->route('levels.index')->with('success', 'Level deleted successfully.');
    }
}
