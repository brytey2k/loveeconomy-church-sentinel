<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionState: int
{
    case PENDING = 0;
    case SUCCESSFUL = 1;
}
