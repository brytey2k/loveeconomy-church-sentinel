<?php

declare(strict_types=1);

namespace App\Data\Ussd;

use App\Enums\PaymentProvider;
use App\Enums\TransactionState;
use App\Enums\TransactionType;

class CreateTransactionData
{
    public function __construct(
        public int $memberId,
        public TransactionType $type,
        public float $amount,
        public TransactionState $status,
        public PaymentProvider $paidThrough,
        public string $orderId,
        public string $paymentType,
        public int $monthPaidFor,
        public int $yearPaidFor,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'member_id' => $this->memberId,
            'type' => $this->type,
            'amount' => $this->amount,
            'status' => $this->status->value,
            'paid_through' => $this->paidThrough->value,
            'order_id' => $this->orderId,
            'payment_type' => $this->paymentType,
            'month_paid_for' => $this->monthPaidFor,
            'year_paid_for' => $this->yearPaidFor,
        ];
    }
}
