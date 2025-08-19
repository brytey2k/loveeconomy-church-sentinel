<?php

declare(strict_types=1);

namespace App\Repositories\Finance;

use App\Models\FxRateHistory;
use Carbon\CarbonImmutable;

class FxRatesHistoryRepository
{
    /**
     * Persist provider response into finance.fx_rates_history.
     *
     * Expected $data shape:
     * - success: bool
     * - timestamp: int (unix epoch seconds)
     * - source: string (e.g., GHS)
     * - quotes: array<string,float> e.g., ['GHSAED' => 0.34]
     * - ... (anything else is stored in meta)
     *
     * Returns number of rows inserted.
     *
     * @param array $data
     */
    public function persistApiResponse(array $data): int
    {
        $quotes = (array) ($data['quotes'] ?? []);
        if ($quotes === []) {
            return 0;
        }

        $timestamp = (int) ($data['timestamp'] ?? time());
        // Use immutable Carbon and normalize to top of the hour in UTC
        $asOfHour = CarbonImmutable::createFromTimestampUTC($timestamp)->startOfHour();

        $source = (string) ($data['source'] ?? '');
        $now = now();

        $rows = [];
        foreach ($quotes as $pair => $rate) {
            $pair = (string) $pair;
            $rate = (float) $rate;

            // Split e.g., GHSAED => base=GHS, quote=AED
            $base = strtoupper(substr($pair, 0, 3));
            $quote = strtoupper(substr($pair, 3));

            if ($base !== $source) {
                continue;
            }

            if ($base === '' || $quote === '') {
                // Skip malformed pairs
                continue;
            }

            $rows[] = [
                'base_currency' => $base,
                'quote_currency' => $quote,
                'rate' => $rate,
                'as_of_hour' => $asOfHour,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return 0;
        }

        // Insert every response without upserting using the model
        $inserted = FxRateHistory::insert($rows);

        return $inserted ? count($rows) : 0;
    }

    /**
     * Get the latest FX rate for a given base->quote currency pair.
     * Returns null if no rate exists.
     *
     * @param string $baseCurrency
     * @param string $quoteCurrency
     */
    public function getLatestRate(string $baseCurrency, string $quoteCurrency): float|null
    {
        $base = strtoupper(trim($baseCurrency));
        $quote = strtoupper(trim($quoteCurrency));

        if ($base === '' || $quote === '') {
            return null;
        }

        return FxRateHistory::query()
            ->where('base_currency', $base)
            ->where('quote_currency', $quote)
            ->orderByDesc('as_of_hour')
            ->value('rate');
    }
}
