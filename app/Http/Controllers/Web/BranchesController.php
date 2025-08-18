<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateBranchData;
use App\Data\UpdateBranchData;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Level;
use App\Repositories\BranchGivingAssignmentRepository;
use App\Repositories\CountriesRepository;
use App\Repositories\GivingTypeRepository;
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
        protected GivingTypeRepository $givingTypeRepository,
        protected BranchGivingAssignmentRepository $branchGivingAssignmentRepository,
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
            'tags' => $this->givingTypeRepository->allChurch(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateBranchData $branchData
     * @param \App\Data\UpdateBranchGivingTypesData $givingData
     */
    public function store(CreateBranchData $branchData, \App\Data\UpdateBranchGivingTypesData $givingData): RedirectResponse
    {
        $branch = $this->branchesRepository->create($branchData);

        // Sync church giving types for the new branch
        $this->branchGivingAssignmentRepository->syncGivingTypes($branch, $givingData->giving_type_keys ?? []);

        return redirect()->route('branches.givings', ['branch' => $branch->id])
            ->with('success', 'Branch created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Branch $branch
     */
    public function edit(Branch $branch): Response
    {
        $branch->load('givingTypes');
        return Inertia::render('Branches/Edit', [
            'branch' => $branch,
            'levels' => Level::all(),
            'countries' => Country::all(),
            'branches' => $this->branchesRepository->allExcept(ids: [$branch->id]),
            'tags' => $this->givingTypeRepository->allChurch(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBranchData $updateBranchData
     * @param Branch $branch
     * @param \App\Data\UpdateBranchGivingTypesData $givingData
     */
    public function update(UpdateBranchData $updateBranchData, Branch $branch, \App\Data\UpdateBranchGivingTypesData $givingData): RedirectResponse
    {
        $branch = $this->branchesRepository->update($branch, $updateBranchData);

        // Sync church giving types after update
        $this->branchGivingAssignmentRepository->syncGivingTypes($branch, $givingData->giving_type_keys ?? []);

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
