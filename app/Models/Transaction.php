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
