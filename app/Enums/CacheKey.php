<?php

declare(strict_types=1);

namespace App\Enums;

enum CacheKey: string
{
    case USSD_SESSION_PREFIX = 'ussd_session:';
}
