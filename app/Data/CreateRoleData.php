<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\AppGuard;
use Spatie\LaravelData\Data;

class CreateRoleData extends Data
{
    public function __construct(
        public string $name,
        public AppGuard $guard = AppGuard::web,
    ) {
    }
}
