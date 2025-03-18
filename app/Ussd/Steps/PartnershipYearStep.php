<?php

declare(strict_types=1);

namespace App\Ussd\Steps;

use App\Dto\UssdInteractionRequestDto;
use App\Enums\UssdAction;
use App\Enums\UssdDataKey;
use App\Enums\UssdStepKey;
use App\Http\Requests\UssdInteractionRequest;
use App\Http\Responses\SuccessResponse;
use App\Repositories\UssdTransactionDataRepository;
use App\Ussd\Contracts\YearStep;
use App\Ussd\Option;
use Exception;

class PartnershipYearStep extends YearStep
{
    protected UssdInteractionRequestDto $requestDto;

    public function __construct(
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
        protected UssdInteractionRequest $request,
        protected AmountStep $amountStep
    ) {
        $this->requestDto = $this->request->toDto();
    }

    public function handle(UssdInteractionRequestDto $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        $message = 'Enter year (example 2025) of payment';

        return SuccessResponse::make(
            data: [
                'SessionId' => $requestDto->sessionId,
                'Type' => 'response',
                'Message' => $message,
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
        return UssdStepKey::PARTNERSHIP_YEAR;
    }

    public function getSelectedOption(int $key): Option
    {
        // validate user response here. If validation fails, throw an exception and let the ussd service let the current step handle it again
        $this->validateYear($this->requestDto->message);
        $txData = $this->ussdTransactionDataRepository->findBySessionId(sessionId: $this->requestDto->sessionId);

        if ($txData === null) {
            $this->ussdTransactionDataRepository->createEmptySessionData(sessionId: $this->requestDto->sessionId);
        } else {
            $this->ussdTransactionDataRepository->update(
                ussdTransactionData: $txData,
                data: [
                    'tx_data' => array_merge($txData['tx_data'], [
                        UssdDataKey::YEAR->value => $this->requestDto->message,
                    ]),
                ]
            );
        }

        // return option that leads to next step
        return new Option(1, 'Amount', $this->amountStep);
    }

    public function getAction(): UssdAction
    {
        return UssdAction::PARTNERSHIP;
    }

    /**
     * @param string $yearString
     *
     * @throws Exception
     */
    protected function validateYear(string $yearString): void
    {
        if (!preg_match('/^\d{4}$/', $yearString)) {
            throw new Exception('Invalid year');
        }
    }
}
