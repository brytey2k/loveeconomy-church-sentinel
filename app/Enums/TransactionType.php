<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionType: int
{
    case TITHE = 1;
    case OFFERING = 2;
    case PARTNERSHIP = 3;
    case SEED = 4;
}
