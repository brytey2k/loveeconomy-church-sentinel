<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreateCountryData;
use App\Data\UpdateCountryData;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CountriesRepository
{
    /**
     * Get all countries without pagination, ordered by name.
     *
     * @return Collection<int, Country>
     */
    public function all(): Collection
    {
        return Country::orderBy('name')->get();
    }

    /**
     * Get all countries with pagination, ordered by name.
     *
     * @return LengthAwarePaginator<Country>
     */
    public function paginate(): LengthAwarePaginator
    {
        return Country::orderBy('name')->paginate();
    }

    /**
     * Get a single country by ID.
     *
     * @param int $id The ID of the country to find
     *
     * @return Country|null The country if found, null otherwise
     */
    public function find(int $id): Country|null
    {
        return Country::find($id);
    }

    /**
     * Create a new country.
     *
     * @param CreateCountryData $countryData The data for creating a new country
     *
     * @return Country The newly created country
     */
    public function create(CreateCountryData $countryData): Country
    {
        return Country::create($countryData->toArray());
    }

    /**
     * Update an existing country.
     *
     * @param Country $country The country to update
     * @param UpdateCountryData $countryData The data for updating the country
     *
     * @return Country The updated country
     */
    public function update(Country $country, UpdateCountryData $countryData): Country
    {
        $country->update($countryData->toArray());

        return $country;
    }

    /**
     * Delete a country.
     *
     * @param Country $country The country to delete
     *
     * @return bool True if the country was deleted successfully, false otherwise
     */
    public function delete(Country $country): bool
    {
        return $country->delete();
    }
}
