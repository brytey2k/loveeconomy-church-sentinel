<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $session_id
 * @property string $phone_number
 * @property string $service_code
 * @property string $operator
 * @property string $log_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession whereLogData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession whereServiceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UssdSession whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UssdSession extends Model
{
    protected $table = 'logs.ussd_sessions';

    protected $fillable = [
        'session_id',
        'phone_number',
        'service_code',
        'operator',
        'log_data',
        'data_to_process',
    ];
}
