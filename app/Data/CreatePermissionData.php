<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\AppGuard;
use Spatie\LaravelData\Data;

class CreatePermissionData extends Data
{
    public function __construct(
        public string $name,
        public AppGuard $guard_name = AppGuard::web,
    ) {
    }
}
