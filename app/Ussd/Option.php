<?php

declare(strict_types=1);

namespace App\Ussd;

use App\Interfaces\UssdStepInterface;

class Option
{
    public function __construct(
        protected int $key,
        protected string $description,
        protected UssdStepInterface|null $nextStep
    ) {
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getNextStep(): UssdStepInterface|null
    {
        return $this->nextStep;
    }
}
