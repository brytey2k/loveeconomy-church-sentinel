<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $ussd_sessions_id
 * @property array<array-key, mixed> $tx_data
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|UssdTransactionData newModelQuery()
 * @method static Builder<static>|UssdTransactionData newQuery()
 * @method static Builder<static>|UssdTransactionData query()
 * @method static Builder<static>|UssdTransactionData whereCreatedAt($value)
 * @method static Builder<static>|UssdTransactionData whereId($value)
 * @method static Builder<static>|UssdTransactionData whereStatus($value)
 * @method static Builder<static>|UssdTransactionData whereTxData($value)
 * @method static Builder<static>|UssdTransactionData whereUpdatedAt($value)
 * @method static Builder<static>|UssdTransactionData whereUssdSessionsId($value)
 *
 * @mixin \Eloquent
 */
class UssdTransactionData extends Model
{
    protected $table = 'logs.ussd_transaction_data';

    protected $fillable = [
        'ussd_sessions_id',
        'tx_data',
        'status',
    ];

    protected $casts = [
        'tx_data' => 'array',
    ];
}
