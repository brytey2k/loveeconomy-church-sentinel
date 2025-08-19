<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreateMemberPaymentData extends Data
{
    public function __construct(
        #[Required, IntegerType]
        #[Min(1)]
        public int $giving_type_id,
        #[Sometimes, IntegerType]
        public int|null $giving_type_system_id,
        #[Required, Date]
        public string $transaction_date, // yyyy-mm-dd

        #[Required, IntegerType]
        #[Min(1)]
        #[Max(12)]
        public int $month_paid_for,
        #[Required, IntegerType]
        #[Min(2000)]
        public int $year_paid_for,
        #[Required, StringType]
        public string $amount, // accept as string to preserve exact value

        #[Required, StringType]
        #[Max(3)]
        public string $currency_code,
    ) {
    }
}
