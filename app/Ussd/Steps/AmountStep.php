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
use Exception;

class AmountStep extends BaseStep implements UssdStepInterface
{
    protected UssdInteractionRequestDto $requestDto;

    public function __construct(
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
        protected UssdInteractionRequest $request,
        protected SendPaymentPromptStep $sendPaymentPromptStep
    ) {
        $this->requestDto = $this->request->toDto();
    }

    public function handle(UssdInteractionRequestDto $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        return SuccessResponse::make(
            data: [
                'SessionId' => $requestDto->sessionId,
                'Type' => UssdResponseType::RESPONSE->value,
                'Message' => 'Enter Amount',
                'Label' => $this->getKey()->getLabel(),
                'ClientState' => $requestDto->clientState
                    ? sprintf('%s/%s', $requestDto->clientState, $this->getKey()->value)
                    : $this->getKey()->value,
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

    public function getKey(): UssdStepKey
    {
        return UssdStepKey::AMOUNT;
    }

    public function getSelectedOption(int $key): Option
    {
        $this->validateInput($this->requestDto->message);

        $txData = $this->ussdTransactionDataRepository->findBySessionId(sessionId: $this->requestDto->sessionId);
        if ($txData === null) {
            $this->ussdTransactionDataRepository->createEmptySessionData(sessionId: $this->requestDto->sessionId);
        } else {
            $this->ussdTransactionDataRepository->update(
                ussdTransactionData: $txData,
                data: [
                    'tx_data' => array_merge($txData['tx_data'], [
                        UssdDataKey::AMOUNT->value => $this->requestDto->message,
                    ]),
                ]
            );
        }

        // return option that leads to next step
        return new Option(1, 'Make payment', $this->sendPaymentPromptStep);
    }

    /**
     * @param string $input
     *
     * @throws Exception
     */
    protected function validateInput(string $input): void
    {
        if (!is_numeric($input)) {
            throw new Exception('Invalid input. Please enter a valid amount');
        }
    }

    public function getAction(): UssdAction
    {
        return UssdAction::UNKNOWN;
    }
}
