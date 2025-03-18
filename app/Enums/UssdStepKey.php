<?php

declare(strict_types=1);

namespace App\Enums;

enum UssdStepKey: string
{
    case WELCOME = 'welcome';
    case TITHE_MONTH = 'tithe_month';
    case TITHE_YEAR = 'tithe_year';
    case AMOUNT = 'amount';
    case PROMPT = 'prompt';
    case PARTNERSHIP_MONTH = 'partnership_month';
    case PARTNERSHIP_YEAR = 'partnership_year';
    case OFFERING = 'offering';
    case SEED = 'seed';

    public function getName(): string
    {
        return match ($this) {
            self::WELCOME => 'Welcome',
            self::TITHE_MONTH => 'Tithe (Month)',
            self::TITHE_YEAR => 'Tithe (Year)',
            self::AMOUNT => 'Amount',
            self::PROMPT => 'Prompt',
            self::PARTNERSHIP_MONTH => 'Partnership (Month)',
            self::PARTNERSHIP_YEAR => 'Partnership (Year)',
            self::OFFERING => 'Offering',
            self::SEED => 'Seed',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::WELCOME => 'Welcome Page',
            self::TITHE_MONTH => 'Tithe (Month)',
            self::TITHE_YEAR => 'Tithe (Year)',
            self::AMOUNT => 'Amount',
            self::PROMPT => 'Prompt',
            self::PARTNERSHIP_MONTH => 'Partnership (Month)',
            self::PARTNERSHIP_YEAR => 'Partnership (Year)',
            self::OFFERING => 'Offering',
            self::SEED => 'Seed',
        };
    }
}
