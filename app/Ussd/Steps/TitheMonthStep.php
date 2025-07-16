<?php

declare(strict_types=1);

namespace App\Ussd\Steps;

use App\Data\Ussd\UssdInteractionRequestData;
use App\Enums\UssdAction;
use App\Enums\UssdDataKey;
use App\Enums\UssdStepKey;
use App\Http\Requests\UssdInteractionRequest;
use App\Http\Responses\SuccessResponse;
use App\Repositories\UssdTransactionDataRepository;
use App\Ussd\Contracts\MonthStep;
use App\Ussd\Option;
use Carbon\Carbon;
use Exception;

class TitheMonthStep extends MonthStep
{
    protected UssdInteractionRequestData $requestDto;

    public function __construct(
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
        protected UssdInteractionRequest $request,
        protected TitheYearStep $titheYearStep
    ) {
        $this->requestDto = $this->request->toDto();
    }

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

    public static function getKey(): UssdStepKey
    {
        return UssdStepKey::TITHE_MONTH;
    }

    public function getSelectedOption(int $key): Option
    {
        $month = $this->resolveMonth($this->requestDto->message);

        $txData = $this->ussdTransactionDataRepository->findBySessionId(sessionId: $this->requestDto->sessionId);
        if ($txData === null) {
            $this->ussdTransactionDataRepository->createEmptySessionData(sessionId: $this->requestDto->sessionId);
        } else {
            $this->ussdTransactionDataRepository
                ->update(
                    ussdTransactionData: $txData,
                    data: [
                        'tx_data' => array_merge($txData['tx_data'], [UssdDataKey::MONTH->value => $month]),
                    ]
                );
        }

        // return option that leads to next step
        return new Option(1, 'Year', $this->titheYearStep);
    }

    public function getAction(): UssdAction
    {
        return UssdAction::TITHE;
    }

    /**
     * @param string $monthString
     *
     * @throws Exception
     */
    protected function resolveMonth(string|null $monthString): string
    {
        if ($monthString === null || $monthString === '') {
            throw new Exception('Month string cannot be empty');
        }

        // check that the string is also a valid month string like 1, 01, Jan, January using Carbon
        foreach (['m', 'M', 'n', 'F'] as $format) {
            try {
                $month = Carbon::createFromFormat($format, $monthString);
            } catch (\Throwable) {
                continue;
            }

            if ($month) {
                return $month->format('m');
            }
        }

        throw new Exception('Invalid month string');
    }
}
