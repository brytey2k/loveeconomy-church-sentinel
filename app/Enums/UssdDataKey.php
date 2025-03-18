<?php

declare(strict_types=1);

namespace App\Enums;

enum UssdDataKey: string
{
    case ACTION = 'action';
    case MONTH = 'month';
    case YEAR = 'year';
    case AMOUNT = 'amount';
}
