<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class HubtelApiClient
{
    public function __construct(
        protected Factory $httpClient,
        protected Repository $config
    ) {
    }

    /**
     * @param string $sessionId
     * @param string $orderId
     *
     * @throws ConnectionException
     */
    public function sendServiceFulfilmentRequest(string $sessionId, string $orderId): PromiseInterface|Response
    {
        Log::info('Sending data to hubtel callback url', [
            'url' => $this->config->get('services.hubtel.fulfilment_url'),
            'sessionId' => $sessionId,
            'orderId' => $orderId,
        ]);

        $response = $this->httpClient
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
                'Authorization' => sprintf(
                    'Basic %s',
                    base64_encode(
                        $this->config->get('services.hubtel.api_id') . ':' . $this->config->get('services.hubtel.api_key')
                    )
                ),
            ])
            ->post($this->config->get('services.hubtel.fulfilment_url'), [
                'SessionId' => $sessionId,
                'OrderId' => $orderId,
                'ServiceStatus' => 'success',
                'MetaData' => null,
            ]);

        Log::info('Response from Hubtel', ['response' => $response->json()]); // todo: response is null, investigate

        return $response;
    }
}
