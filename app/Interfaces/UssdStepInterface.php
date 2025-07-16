<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Data\Ussd\UssdInteractionRequestData;
use App\Enums\UssdAction;
use App\Enums\UssdStepKey;
use App\Exceptions\MissingStepException;
use App\Ussd\Option;
use Exception;

interface UssdStepInterface
{
    public function handle(UssdInteractionRequestData $requestDto, string|null $message = null, bool $replace = false): mixed;

    /**
     * @return array<Option>
     */
    public function getOptions(): array;
    public static function getKey(): UssdStepKey;

    /**
     * @param int $key
     *
     * @throws MissingStepException|Exception
     */
    public function getSelectedOption(int $key): Option;
    public function getAction(): UssdAction;
}
