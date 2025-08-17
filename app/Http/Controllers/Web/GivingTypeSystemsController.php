<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateGivingTypeSystemData;
use App\Data\UpdateGivingTypeSystemData;
use App\Http\Controllers\Controller;
use App\Models\GivingType;
use App\Models\GivingTypeSystem;
use App\Repositories\GivingTypeRepository;
use App\Repositories\GivingTypeSystemRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GivingTypeSystemsController extends Controller
{
    public function __construct(
        protected GivingTypeSystemRepository $systems,
        protected GivingTypeRepository $givingTypes,
    ) {
    }

    public function index(Request $request, GivingType|null $givingType = null): Response
    {
        // Prefer route param, fallback to query param for backward compatibility
        $givingTypeId = $givingType?->id ?? ($request->has('giving_type_id') ? (int) $request->integer('giving_type_id') : null);
        return Inertia::render('GivingTypeSystems/Index', [
            'systems' => $this->systems->paginate($givingTypeId),
            'givingTypeId' => $givingTypeId,
        ]);
    }

    public function create(GivingType $givingType): Response
    {
        $givingTypeId = $givingType->id;
        return Inertia::render('GivingTypeSystems/Create', [
            'parentOptions' => $givingTypeId ? $this->systems->allForType($givingTypeId) : [],
            'givingTypeId' => $givingTypeId,
        ]);
    }

    public function store(CreateGivingTypeSystemData $data): RedirectResponse
    {
        $created = $this->systems->create($data);
        // Redirect to Giving Type Systems index scoped by route param
        return redirect()
            ->route('giving-type-systems.index-for-type', ['givingType' => $created->giving_type_id])
            ->with('success', 'Giving type system created successfully.');
    }

    public function edit(GivingTypeSystem $givingTypeSystem): Response
    {
        return Inertia::render('GivingTypeSystems/Edit', [
            'system' => $givingTypeSystem->load(['givingType', 'parent']),
            'givingTypes' => $this->givingTypes->all(),
            'parentOptions' => $this->systems->allForType($givingTypeSystem->giving_type_id)->where('id', '!=', $givingTypeSystem->id)->values(),
        ]);
    }

    public function update(UpdateGivingTypeSystemData $data, GivingTypeSystem $givingTypeSystem): RedirectResponse
    {
        $this->systems->update($givingTypeSystem, $data);
        return redirect()->route('giving-type-systems.index')->with('success', 'Giving type system updated successfully.');
    }

    public function destroy(GivingTypeSystem $givingTypeSystem): RedirectResponse
    {
        $this->systems->delete($givingTypeSystem);
        return redirect()->route('giving-type-systems.index')->with('success', 'Giving type system deleted successfully.');
    }
}
