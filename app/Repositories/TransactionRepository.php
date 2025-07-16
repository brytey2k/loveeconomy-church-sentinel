<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\Ussd\CreateTransactionData;
use App\Models\Transaction;

class TransactionRepository
{
    public function create(CreateTransactionData $data): Transaction
    {
        return Transaction::query()->create($data->toArray());
    }
}
