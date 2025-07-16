<?php

declare(strict_types=1);

namespace App\Ussd\Contracts;

use App\Data\Ussd\UssdInteractionRequestData;
use App\Enums\UssdAction;
use App\Enums\UssdStepKey;
use App\Http\Responses\SuccessResponse;
use App\Interfaces\UssdStepInterface;
use App\Ussd\Option;

abstract class MonthStep extends BaseStep implements UssdStepInterface
{
    protected UssdInteractionRequestData $requestDto;

    public function handle(UssdInteractionRequestData $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        $message = 'Enter month (Jan to Dec) of payment';

        return SuccessResponse::make(
            data: [
                'SessionId' => $requestDto->sessionId,
                'Type' => 'response',
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

    abstract public static function getKey(): UssdStepKey;

    abstract public function getSelectedOption(int $key): Option;

    abstract public function getAction(): UssdAction;
}
