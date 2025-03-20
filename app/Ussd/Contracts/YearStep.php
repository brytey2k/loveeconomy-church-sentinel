<?php

declare(strict_types=1);

namespace App\Ussd\Contracts;

use App\Dto\UssdInteractionRequestDto;
use App\Enums\UssdAction;
use App\Enums\UssdStepKey;
use App\Http\Responses\SuccessResponse;
use App\Interfaces\UssdStepInterface;
use App\Ussd\Option;

abstract class YearStep extends BaseStep implements UssdStepInterface
{
    protected UssdInteractionRequestDto $requestDto;

    public function handle(UssdInteractionRequestDto $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        $message = 'Enter year (example 2025) of payment';

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
