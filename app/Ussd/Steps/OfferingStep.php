<?php

declare(strict_types=1);

namespace App\Ussd\Steps;

use App\Dto\UssdInteractionRequestDto;
use App\Enums\UssdAction;
use App\Enums\UssdStepKey;
use App\Interfaces\UssdStepInterface;
use App\Ussd\Contracts\BaseStep;
use App\Ussd\Option;

class OfferingStep extends BaseStep implements UssdStepInterface
{
    public function __construct(
        protected AmountStep $amountStep
    ) {
    }

    public function handle(UssdInteractionRequestDto $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        // append offering action to client state and proxy this to the amount step
        $requestDto->clientState = sprintf('%s/%s', $requestDto->clientState, static::getKey()->value);
        return $this->amountStep->handle($requestDto);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public static function getKey(): UssdStepKey
    {
        return UssdStepKey::OFFERING;
    }

    public function getSelectedOption(int $key): Option
    {
        // todo: complete offering steps
        // return option that leads to next step
        return new Option(1, 'Amount', $this->amountStep);
    }

    public function getAction(): UssdAction
    {
        return UssdAction::OFFERING;
    }
}
