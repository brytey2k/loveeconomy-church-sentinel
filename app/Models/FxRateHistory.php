<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
