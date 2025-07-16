<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class UpdatePermissionData extends Data
{
    public function __construct(
        public string $name
    ) {
    }
}
