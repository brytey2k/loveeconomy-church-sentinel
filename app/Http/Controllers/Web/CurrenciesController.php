<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateCurrencyData;
use App\Data\UpdateCurrencyData;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Repositories\CurrencyRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CurrenciesController extends Controller
{
    public function __construct(
        protected CurrencyRepository $currencyRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Currencies/Index', [
            'currencies' => $this->currencyRepository->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Currencies/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCurrencyData $currencyData
     */
    public function store(CreateCurrencyData $currencyData): RedirectResponse
    {
        $this->currencyRepository->create($currencyData);

        return redirect()->route('currencies.index')->with('success', 'Currency created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Currency $currency
     */
    public function edit(Currency $currency): Response
    {
        return Inertia::render('Currencies/Edit', [
            'currency' => $currency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCurrencyData $updateCurrencyData
     * @param Currency $currency
     */
    public function update(UpdateCurrencyData $updateCurrencyData, Currency $currency): RedirectResponse
    {
        $this->currencyRepository->update($currency, $updateCurrencyData);

        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Currency $currency
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        $this->currencyRepository->delete($currency);

        return redirect()->route('currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
