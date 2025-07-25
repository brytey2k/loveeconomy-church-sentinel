<?php

declare(strict_types=1);

namespace App\Ussd\Contracts;

use App\Data\Ussd\UssdInteractionRequestData;
use App\Exceptions\DuplicateOptionKeysException;
use App\Ussd\Option;

abstract class BaseStep
{
    /**
     * @var array<Option>
     */
    protected array $options = [];

    /**
     * @throws DuplicateOptionKeysException
     */
    public function validateOptionKeys(): void
    {
        $options = $this->getOptions();
        $keys = array_map(static fn ($option) => $option->getKey(), $options);
        $uniqueKeys = array_unique($keys);

        if (count($keys) !== count($uniqueKeys)) {
            throw new DuplicateOptionKeysException(message: 'Application error: Oops... we messed up the options!');
        }
    }

    /**
     * @param Option $option
     *
     * @throws DuplicateOptionKeysException
     *
     * @return BaseStep
     */
    public function addOption(Option $option): self
    {
        $this->options[] = $option;
        $this->validateOptionKeys();

        return $this;
    }

    /**
     * @return array<Option>
     */
    abstract public function getOptions(): array;

    final public function isInitiationStep(UssdInteractionRequestData $requestDto): bool
    {
        return ($requestDto->clientState === '' || $requestDto->clientState === null) && $requestDto->type === 'Initiation';
    }

    final public function isTimeoutStep(UssdInteractionRequestData $requestDto): bool
    {
        return $requestDto->type === 'Timeout';
    }
}
