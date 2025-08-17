<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Repositories\Finance\FxRatesHistoryRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchFxRatesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string|null $source = null
    ) {
    }

    /**
     * Execute the job.
     *
     * @param FxRatesHistoryRepository $fxRatesHistoryRepository
     *
     * @throws Exception
     */
    public function handle(FxRatesHistoryRepository $fxRatesHistoryRepository): void
    {
        $baseUrl = rtrim((string) config('exchangerate.base_url'), '/');
        $accessKey = (string) config('exchangerate.access_key');
        $source = $this->source ?: (string) config('exchangerate.source', 'GHS');
        $timeout = (int) config('exchangerate.timeout', 15);

        if ($accessKey === '') {
            // Fail fast if we don't have an access key configured
            throw new Exception('Exchange rate access key is not configured. Please set EXCHANGE_RATE_ACCESS_KEY in .env');
        }

        $url = sprintf('%s/live', $baseUrl);

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->get($url, [
                'source' => $source,
                'access_key' => $accessKey,
            ]);

        if (!$response->ok()) {
            throw new Exception(sprintf('Exchange rate request failed with status %s', $response->status()));
        }

        $data = $response->json();

        // Expecting shape:
        // {
        //   success: true|false,
        //   timestamp: int,
        //   source: string,
        //   quotes: array<string, float>
        // }
        if (!is_array($data) || !($data['success'] ?? false)) {
            // If success is false, we should fail this job
            $errorMessage = is_array($data) && isset($data['error']) ? json_encode($data['error']) : 'API returned unsuccessful response';
            throw new Exception('Failed to fetch FX rates: '.$errorMessage);
        }

        $quotes = (array) ($data['quotes'] ?? []);
        $timestamp = (int) ($data['timestamp'] ?? time());
        $sourceFromApi = (string) ($data['source'] ?? $source);

        // Persist into finance.fx_rates_history using repository
        $persisted = $fxRatesHistoryRepository->persistApiResponse($data);

        // Cache latest rate for each pair as fx:rates:{pair in lowercase}
        $prefix = (string) config('exchangerate.cache_prefix', 'fx:rates');
        foreach ($quotes as $pair => $rate) {
            $pairKey = strtolower((string) $pair); // e.g., ghsaed
            Cache::put($prefix . ':' . $pairKey, (float) $rate, now()->addHours(12));
        }

        Log::info('Fetched FX rates', [
            'source' => $sourceFromApi,
            'timestamp' => $timestamp,
            'count' => count($quotes),
            'persisted' => $persisted,
        ]);
    }
}
