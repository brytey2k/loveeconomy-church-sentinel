<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Present;
use Spatie\LaravelData\Data;

class UpdateBranchGivingTypesData extends Data
{
    /**
     * @param array<int,string> $giving_type_keys
     */
    public function __construct(
        #[Present]
        #[ArrayType]
        public array $giving_type_keys = [],
    ) {
    }
}
