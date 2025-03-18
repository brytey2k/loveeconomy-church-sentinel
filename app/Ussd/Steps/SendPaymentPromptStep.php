<?php

declare(strict_types=1);

namespace App\Ussd\Steps;

use App\Dto\UssdInteractionRequestDto;
use App\Enums\UssdAction;
use App\Enums\UssdDataKey;
use App\Enums\UssdResponseType;
use App\Enums\UssdStepKey;
use App\Http\Requests\UssdInteractionRequest;
use App\Http\Responses\SuccessResponse;
use App\Interfaces\UssdStepInterface;
use App\Repositories\UssdTransactionDataRepository;
use App\Ussd\Option;

class SendPaymentPromptStep extends BaseStep implements UssdStepInterface
{
    protected UssdInteractionRequestDto $requestDto;

    public function __construct(
        protected UssdInteractionRequest $request,
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
    ) {
        $this->requestDto = $this->request->toDto();
    }

    public function handle(UssdInteractionRequestDto $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        $txData = $this->ussdTransactionDataRepository->findBySessionId($requestDto->sessionId);

        return SuccessResponse::make(
            data: [
                'SessionId' => $requestDto->sessionId,
                'Type' => UssdResponseType::ADD_TO_CART->value,
                'Message' => 'The request has been submitted. Please wait for a payment prompt soon',
                'Item' => [
                    'ItemName' => 'Payment',
                    'Qty' => 1,
                    'Price' => $txData->tx_data[UssdDataKey::AMOUNT->value],
                ],
                'Label' => $this->getKey()->getLabel(),
                'DataType' => 'display',
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

    public function getKey(): UssdStepKey
    {
        return UssdStepKey::PROMPT;
    }

    public function getSelectedOption(int $key): Option
    {
        // return option that leads to next step
        return new Option(1, 'Payment Prompt', null);
    }

    public function getAction(): UssdAction
    {
        return UssdAction::UNKNOWN;
    }
}
