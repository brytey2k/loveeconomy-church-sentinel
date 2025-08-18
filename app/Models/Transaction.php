<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $member_id
 * @property string $type
 * @property string $amount
 * @property int $status
 * @property int $month_paid_for
 * @property int $year_paid_for
 * @property string|null $paid_through The payment provider/processor used
 * @property string|null $order_id The order ID from the payment provider
 * @property string|null $payment_type whether momo, card, etc
 * @property string|null $session_id session id if it was a ussd transaction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereMonthPaidFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaidThrough($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereYearPaidFor($value)
 *
 * @property string $tx_date the actual date and time of the transaction
 * @property int $amount_raw Original amount stored in minor units (integer). Example: GHS 12.34 => 1234. Avoid floats.
 * @property string $currency ISO 4217 currency code of the original/booked amount (e.g., GHS, USD, EUR).
 * @property string $fx_rate Rate used to convert currency to reporting_currency at booking time. 1 when currency == reporting_currency.
 * @property string $reporting_currency ISO 4217 code used for consolidated reporting (e.g., GHS). Stored per row for explicitness/audit.
 * @property int $converted_raw Amount converted to reporting_currency in minor units using fx_rate and deterministic rounding.
 * @property string|null $original_amount_entered The exact user-entered amount text preserved for audit (e.g., "100").
 * @property string|null $original_amount_currency Currency as originally entered by the user (ISO 4217). Useful if parsing differs from stored currency.
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAmountRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereConvertedRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereFxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereOriginalAmountCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereOriginalAmountEntered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereReportingCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTxDate($value)
 *
 * @property int $giving_type_id
 * @property int|null $giving_type_system_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereGivingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereGivingTypeSystemId($value)
 *
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    protected $table = 'finance.transactions';

    protected $fillable = [
        'member_id',
        'type',
        'amount',
        'status',
        'month_paid_for',
        'year_paid_for',
        'paid_through',
        'order_id',
        'payment_type',
        'session_id',
    ];
}
