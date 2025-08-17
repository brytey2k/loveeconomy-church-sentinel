<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Present;
use Spatie\LaravelData\Data;

class UpdateMemberGivingTypeSystemsData extends Data
{
    /**
     * @param array<int,int> $system_ids
     */
    public function __construct(
        #[Present]
        #[ArrayType]
        public array $system_ids = [],
    ) {
    }
}
