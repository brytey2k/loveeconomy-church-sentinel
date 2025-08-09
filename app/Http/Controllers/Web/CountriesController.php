<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateCountryData;
use App\Data\UpdateCountryData;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Repositories\CountriesRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CountriesController extends Controller
{
    public function __construct(
        protected CountriesRepository $countriesRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Countries/Index', [
            'countries' => $this->countriesRepository->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Countries/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCountryData $countryData
     */
    public function store(CreateCountryData $countryData): RedirectResponse
    {
        $this->countriesRepository->create($countryData);

        return redirect()->route('countries.index')->with('success', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Country $country
     */
    public function edit(Country $country): Response
    {
        return Inertia::render('Countries/Edit', [
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCountryData $updateCountryData
     * @param Country $country
     */
    public function update(UpdateCountryData $updateCountryData, Country $country): RedirectResponse
    {
        $this->countriesRepository->update($country, $updateCountryData);

        return redirect()->route('countries.index')->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Country $country
     */
    public function destroy(Country $country): RedirectResponse
    {
        $this->countriesRepository->delete($country);

        return redirect()->route('countries.index')->with('success', 'Country deleted successfully.');
    }
}
