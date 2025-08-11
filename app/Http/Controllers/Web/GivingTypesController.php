<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateGivingTypeData;
use App\Data\UpdateGivingTypeData;
use App\Http\Controllers\Controller;
use App\Models\GivingType;
use App\Repositories\GivingTypeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GivingTypesController extends Controller
{
    public function __construct(
        protected GivingTypeRepository $givingTypeRepository,
    ) {
    }

    public function index(Request $request): Response
    {
        $onlyTrashed = (bool) $request->boolean('trashed');
        return Inertia::render('GivingTypes/Index', [
            'givingTypes' => $this->givingTypeRepository->paginate($onlyTrashed),
            'showingTrashed' => $onlyTrashed,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('GivingTypes/Create');
    }

    public function store(CreateGivingTypeData $data): RedirectResponse
    {
        $this->givingTypeRepository->create($data->toArray());
        return redirect()->route('giving-types.index')->with('success', 'Giving type created successfully.');
    }

    public function edit(GivingType $givingType): Response
    {
        return Inertia::render('GivingTypes/Edit', [
            'givingType' => $givingType,
        ]);
    }

    public function update(UpdateGivingTypeData $data, GivingType $givingType): RedirectResponse
    {
        $this->givingTypeRepository->update($givingType, $data->toArray());
        return redirect()->route('giving-types.index')->with('success', 'Giving type updated successfully.');
    }

    public function destroy(GivingType $givingType): RedirectResponse
    {
        $this->givingTypeRepository->delete($givingType);
        return redirect()->route('giving-types.index')->with('success', 'Giving type deleted successfully.');
    }

    public function restore(int $givingTypeId): RedirectResponse
    {
        $givingType = GivingType::onlyTrashed()->findOrFail($givingTypeId);
        $this->givingTypeRepository->restore($givingType);
        return redirect()->route('giving-types.index', ['trashed' => 1])->with('success', 'Giving type restored successfully.');
    }
}
