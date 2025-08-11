<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Indicates whether a giving type is contributed by an individual or by the church.
 */
enum ContributionType: string
{
    case INDIVIDUAL = 'individual';
    case CHURCH = 'church';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn (self $c) => $c->value, self::cases());
    }
}
