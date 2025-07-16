<?php

declare(strict_types=1);

namespace App\Enums;

enum AppGuard: string
{
    case web = 'web';
    case api = 'api';
}
