<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\CreateTransactionDto;
use App\Models\Transaction;

class TransactionRepository
{
    public function create(CreateTransactionDto $data): Transaction
    {
        return Transaction::query()->create($data->toArray());
    }
}
