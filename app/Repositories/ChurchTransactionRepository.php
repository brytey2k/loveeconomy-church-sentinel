<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreateChurchTransactionData;
use App\Models\ChurchTransaction;

class ChurchTransactionRepository
{
    public function create(CreateChurchTransactionData $data): ChurchTransaction
    {
        return ChurchTransaction::query()->create($data->toArray());
    }
}
