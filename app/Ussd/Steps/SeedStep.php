<?php

declare(strict_types=1);

namespace App\Ussd\Steps;

use App\Data\Ussd\UssdInteractionRequestData;
use App\Enums\UssdAction;
use App\Enums\UssdResponseType;
use App\Enums\UssdStepKey;
use App\Http\Responses\SuccessResponse;
use App\Interfaces\UssdStepInterface;
use App\Ussd\Contracts\BaseStep;
use App\Ussd\Option;

class SeedStep extends BaseStep implements UssdStepInterface
{
    public function __construct(
        protected AmountStep $amountStep
    ) {
    }

    public function handle(UssdInteractionRequestData $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        $message = 'Enter month and year of payment';

        return SuccessResponse::make(
            data: [
                'SessionId' => $requestDto->sessionId,
                'Type' => UssdResponseType::RESPONSE->value,
                'Message' => $message,
                'Label' => static::getKey()->getLabel(),
                'ClientState' => $requestDto->clientState
                    ? sprintf('%s/%s', $requestDto->clientState, static::getKey()->value)
                    : static::getKey()->value,
                'DataType' => 'input',
                'FieldType' => 'text',
            ],
            statusCode: 200,
            headers: ['Content-Type' => 'application/json']
        );
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public static function getKey(): UssdStepKey
    {
        return UssdStepKey::SEED;
    }

    public function getSelectedOption(int $key): Option
    {
        // todo: finish seed steps
        // return option that leads to next step
        return new Option(1, 'Amount', $this->amountStep);
    }

    public function getAction(): UssdAction
    {
        return UssdAction::SEED;
    }
}
