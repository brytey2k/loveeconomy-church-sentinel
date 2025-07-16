<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\Ussd\UssdInteractionRequestData;
use App\Models\UssdSession;

class UssdSessionRepository
{
    public function __construct()
    {
    }

    public function create(UssdInteractionRequestData $dto): UssdSession
    {
        return UssdSession::query()->create([
            'session_id' => $dto->sessionId,
            'phone_number' => $dto->mobile,
            'service_code' => $dto->serviceCode,
            'operator' => $dto->operator,
            'log_data' => json_encode([
                [
                    'request' => $dto->toArray()
                ]
            ]),
        ]);
    }
}
