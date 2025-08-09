<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateBranchData;
use App\Data\UpdateBranchData;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Level;
use App\Repositories\CountriesRepository;
use App\Repositories\Structure\BranchesRepository;
use App\Repositories\Structure\LevelRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BranchesController extends Controller
{
    public function __construct(
        protected BranchesRepository $branchesRepository,
        protected LevelRepository $levelRepository,
        protected CountriesRepository $countriesRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Branches/Index', [
            'branches' => $this->branchesRepository->list(relations: ['level', 'country', 'ltreeParent']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Branches/Create', [
            'levels' => $this->levelRepository->all(),
            'countries' => $this->countriesRepository->all(),
            'branches' => $this->branchesRepository->all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateBranchData $branchData
     */
    public function store(CreateBranchData $branchData): RedirectResponse
    {
        $this->branchesRepository->create($branchData);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Branch $branch
     */
    public function edit(Branch $branch): Response
    {
        return Inertia::render('Branches/Edit', [
            'branch' => $branch,
            'levels' => Level::all(),
            'countries' => Country::all(),
            'branches' => $this->branchesRepository->allExcept(ids: [$branch->id]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBranchData $updateBranchData
     * @param Branch $branch
     */
    public function update(UpdateBranchData $updateBranchData, Branch $branch): RedirectResponse
    {
        $this->branchesRepository->update($branch, $updateBranchData);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Branch $branch
     */
    public function destroy(Branch $branch): RedirectResponse
    {
        $this->branchesRepository->delete($branch);

        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }
}
