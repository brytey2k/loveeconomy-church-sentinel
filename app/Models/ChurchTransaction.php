<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $tx_date the actual date and time of the transaction
 * @property int $branch_id
 * @property int $giving_type_id
 * @property int|null $giving_type_system_id
 * @property int $status
 * @property int $month_paid_for
 * @property int $year_paid_for
 * @property string|null $paid_through The payment provider/processor used
 * @property string|null $order_id The order ID from the payment provider
 * @property string|null $payment_type whether momo, card, etc
 * @property string|null $session_id session id if it was a ussd transaction
 * @property int $amount_raw Original amount stored in minor units (integer). Example: GHS 12.34 => 1234. Avoid floats.
 * @property string $currency ISO 4217 currency code of the original/booked amount (e.g., GHS, USD, EUR).
 * @property string $fx_rate Rate used to convert currency to reporting_currency at booking time. 1 when currency == reporting_currency.
 * @property string $reporting_currency ISO 4217 code used for consolidated reporting (e.g., GHS). Stored per row for explicitness/audit.
 * @property int $converted_raw Amount converted to reporting_currency in minor units using fx_rate and deterministic rounding.
 * @property string|null $original_amount_entered The exact user-entered amount text preserved for audit (e.g., "100").
 * @property string|null $original_amount_currency Currency as originally entered by the user (ISO 4217). Useful if parsing differs from stored currency.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereAmountRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereConvertedRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereFxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereGivingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereGivingTypeSystemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereMonthPaidFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereOriginalAmountCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereOriginalAmountEntered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction wherePaidThrough($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereReportingCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereTxDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChurchTransaction whereYearPaidFor($value)
 *
 * @mixin \Eloquent
 */
class ChurchTransaction extends Model
{
    protected $table = 'finance.church_transactions';

    protected $fillable = [
        'branch_id',
        'giving_type_id',
        'giving_type_system_id',
        'status',
        'month_paid_for',
        'year_paid_for',
        'paid_through',
        'order_id',
        'payment_type',
        'session_id',
        'tx_date',
        'amount_raw',
        'currency',
        'fx_rate',
        'reporting_currency',
        'converted_raw',
        'original_amount_entered',
        'original_amount_currency',
    ];
}
