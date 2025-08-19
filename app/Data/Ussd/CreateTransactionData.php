<?php

declare(strict_types=1);

namespace App\Data\Ussd;

use App\Enums\PaymentProvider;
use App\Enums\TransactionState;
use App\Enums\TransactionType;

class CreateTransactionData
{
    public function __construct(
        // USSD-related fields (optional)
        public int|null $memberId = null,
        public TransactionType|null $type = null,
        public float|null $amount = null,
        public TransactionState|null $status = null,
        public PaymentProvider|null $paidThrough = null,
        public string|null $orderId = null,
        public string|null $paymentType = null,
        public int|null $monthPaidFor = null,
        public int|null $yearPaidFor = null,
        // Web/Reporting transaction creation fields (optional)
        public \DateTimeInterface|null $txDate = null,
        public int|null $givingTypeId = null,
        public int|null $givingTypeSystemId = null,
        public int|null $amountRaw = null,
        public string|null $currency = null,
        public string|null $fxRate = null,
        public string|null $reportingCurrency = null,
        public int|null $convertedRaw = null,
        public int|null $branchId = null,
        public string|null $branchReportingCurrency = null,
        public int|null $branchConvertedRaw = null,
        public string|null $originalAmountEntered = null,
        public string|null $originalAmountCurrency = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        // Shared / USSD fields
        if ($this->memberId !== null) {
            $data['member_id'] = $this->memberId;
        }
        if ($this->type !== null) {
            $data['type'] = $this->type;
        }
        if ($this->amount !== null) {
            $data['amount'] = $this->amount;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status->value;
        }
        if ($this->paidThrough !== null) {
            $data['paid_through'] = $this->paidThrough->value;
        }
        if ($this->orderId !== null) {
            $data['order_id'] = $this->orderId;
        }
        if ($this->paymentType !== null) {
            $data['payment_type'] = $this->paymentType;
        }
        if ($this->monthPaidFor !== null) {
            $data['month_paid_for'] = $this->monthPaidFor;
        }
        if ($this->yearPaidFor !== null) {
            $data['year_paid_for'] = $this->yearPaidFor;
        }

        // Web/Reporting specific fields
        if ($this->txDate !== null) {
            $data['tx_date'] = $this->txDate;
        }
        if ($this->givingTypeId !== null) {
            $data['giving_type_id'] = $this->givingTypeId;
        }
        if ($this->givingTypeSystemId !== null) {
            $data['giving_type_system_id'] = $this->givingTypeSystemId;
        }
        if ($this->amountRaw !== null) {
            $data['amount_raw'] = $this->amountRaw;
        }
        if ($this->currency !== null) {
            $data['currency'] = $this->currency;
        }
        if ($this->fxRate !== null) {
            $data['fx_rate'] = $this->fxRate;
        }
        if ($this->reportingCurrency !== null) {
            $data['reporting_currency'] = $this->reportingCurrency;
        }
        if ($this->convertedRaw !== null) {
            $data['converted_raw'] = $this->convertedRaw;
        }
        if ($this->branchId !== null) {
            $data['branch_id'] = $this->branchId;
        }
        if ($this->branchReportingCurrency !== null) {
            $data['branch_reporting_currency'] = $this->branchReportingCurrency;
        }
        if ($this->branchConvertedRaw !== null) {
            $data['branch_converted_raw'] = $this->branchConvertedRaw;
        }
        if ($this->originalAmountEntered !== null) {
            $data['original_amount_entered'] = $this->originalAmountEntered;
        }
        if ($this->originalAmountCurrency !== null) {
            $data['original_amount_currency'] = $this->originalAmountCurrency;
        }

        return $data;
    }
}
