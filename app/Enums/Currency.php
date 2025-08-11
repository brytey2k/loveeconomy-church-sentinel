<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Currencies of interest for the application.
 *
 * String-backed enum with ISO 4217 currency representations.
 * Extend this list as your business expands to new currencies.
 */
enum Currency: string
{
    case USD = 'USD'; // US Dollar
    case EUR = 'EUR'; // Euro
    case GHS = 'GHS'; // Ghanaian Cedi
    case GBP = 'GBP'; // British Pound
    case NGN = 'NGN'; // Nigerian Naira
    case KES = 'KES'; // Kenyan Shilling
    case ZAR = 'ZAR'; // South African Rand
    case JPY = 'JPY'; // Japanese Yen (0-decimal)
    case TND = 'TND'; // Tunisian Dinar (3-decimal)
    case JOD = 'JOD'; // Jordanian Dinar (3-decimal)

    /**
     * Return all currency codes as an array of strings.
     *
     * @return string[]
     */
    public static function codes(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
