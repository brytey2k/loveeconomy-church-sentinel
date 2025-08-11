<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $base_currency Base currency ISO 4217 code (e.g., USD). See App\Enums\Currency for allowed values.
 * @property string $quote_currency Quote currency ISO 4217 code (e.g., GHS). See App\Enums\Currency for allowed values.
 * @property string $rate FX rate as base->quote (1 base_currency equals `rate` quote_currency). High precision decimal.
 * @property \Illuminate\Support\Carbon $as_of_hour Timestamp of the hourly snapshot (UTC recommended). Should be truncated to the start of the hour.
 * @property string|null $source Source of the rate snapshot (e.g., ECB, OXR, internal).
 * @property array<array-key, mixed>|null $meta Optional provider payload or annotations for audit.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder<static>|FxRateHistory newModelQuery()
 * @method static Builder<static>|FxRateHistory newQuery()
 * @method static Builder<static>|FxRateHistory query()
 * @method static Builder<static>|FxRateHistory whereAsOfHour($value)
 * @method static Builder<static>|FxRateHistory whereBaseCurrency($value)
 * @method static Builder<static>|FxRateHistory whereCreatedAt($value)
 * @method static Builder<static>|FxRateHistory whereId($value)
 * @method static Builder<static>|FxRateHistory whereMeta($value)
 * @method static Builder<static>|FxRateHistory whereQuoteCurrency($value)
 * @method static Builder<static>|FxRateHistory whereRate($value)
 * @method static Builder<static>|FxRateHistory whereSource($value)
 * @method static Builder<static>|FxRateHistory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FxRateHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'finance.fx_rates_history';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'base_currency',
        'quote_currency',
        'rate',
        'as_of_hour',
        'source',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'as_of_hour' => 'datetime',
        'meta' => 'array',
    ];
}
