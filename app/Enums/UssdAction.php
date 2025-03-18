<?php

declare(strict_types=1);

namespace App\Enums;

enum UssdAction: string
{
    case TITHE = 'tithe';
    case OFFERING = 'offering';
    case PARTNERSHIP = 'partnership';
    case SEED = 'seed';
    case UNKNOWN = '';

    public function toTransactionType(): TransactionType
    {
        return match ($this) {
            self::TITHE => TransactionType::TITHE,
            self::OFFERING => TransactionType::OFFERING,
            self::PARTNERSHIP => TransactionType::PARTNERSHIP,
            self::SEED => TransactionType::SEED,
            default => throw new \InvalidArgumentException('Invalid UssdAction'),
        };
    }
}
