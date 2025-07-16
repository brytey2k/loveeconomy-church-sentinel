<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\UssdTransactionState;
use App\Models\UssdTransactionData;

class UssdTransactionDataRepository
{
    public function __construct()
    {
    }

    public function findBySessionId(string $sessionId): UssdTransactionData|null
    {
        return UssdTransactionData::query()->where('ussd_sessions_id', $sessionId)->first();
    }

    public function createEmptySessionData(string $sessionId): UssdTransactionData
    {
        return UssdTransactionData::query()->create([
            'ussd_sessions_id' => $sessionId,
            'status' => UssdTransactionState::PENDING,
            'tx_data' => [],
        ]);
    }

    /**
     * @param UssdTransactionData $ussdTransactionData
     * @param array<string, mixed> $data
     *
     * @return bool
     */
    public function update(UssdTransactionData $ussdTransactionData, array $data): bool
    {
        return $ussdTransactionData->update($data);
    }
}
