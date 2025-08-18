<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateChurchTransactionData extends Data
{
    public function __construct(
        public int $branch_id,
        public int $giving_type_id,
        public int|null $giving_type_system_id,
        public int $status,
        public int $month_paid_for,
        public int $year_paid_for,
        public string $tx_date,
        public int $amount_raw,
        public string $currency,
        public string $fx_rate,
        public string $reporting_currency,
        public int $converted_raw,
        public string|null $paid_through = null,
        public string|null $order_id = null,
        public string|null $payment_type = null,
        public string|null $session_id = null,
        public string|null $original_amount_entered = null,
        public string|null $original_amount_currency = null,
    ) {
    }
}
