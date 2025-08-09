<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreateCurrencyData;
use App\Data\UpdateCurrencyData;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CurrencyRepository
{
    /**
     * Get all currencies without pagination, ordered by name.
     *
     * @param array<string> $relations The relations to eager load
     *
     * @return Collection<int, Currency>
     */
    public function all(array $relations = []): Collection
    {
        return Currency::with($relations)->orderBy('name')->get();
    }

    /**
     * Get all currencies with pagination, ordered by name.
     *
     * @param array<string> $relations The relations to eager load
     *
     * @return LengthAwarePaginator<Currency>
     */
    public function paginate(array $relations = []): LengthAwarePaginator
    {
        return Currency::with($relations)->orderBy('name')->paginate();
    }

    /**
     * Get a single currency by ID.
     *
     * @param int $id The ID of the currency to find
     *
     * @return Currency|null The currency if found, null otherwise
     */
    public function find(int $id): Currency|null
    {
        return Currency::find($id);
    }

    /**
     * Create a new currency.
     *
     * @param CreateCurrencyData $data The data for creating a new currency
     *
     * @return Currency The newly created currency
     */
    public function create(CreateCurrencyData $data): Currency
    {
        return Currency::query()->create($data->toArray());
    }

    /**
     * Update an existing currency.
     *
     * @param Currency $currency The currency to update
     * @param UpdateCurrencyData $data The data for updating the currency
     *
     * @return Currency The updated currency
     */
    public function update(Currency $currency, UpdateCurrencyData $data): Currency
    {
        $currency->update($data->toArray());

        return $currency;
    }

    /**
     * Delete a currency.
     *
     * @param Currency $currency The currency to delete
     *
     * @return bool True if the currency was deleted successfully, false otherwise
     */
    public function delete(Currency $currency): bool
    {
        return $currency->delete();
    }
}
