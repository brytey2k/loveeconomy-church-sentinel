<?php

declare(strict_types=1);

namespace App\Ussd\Steps;

use App\Data\Ussd\UssdInteractionRequestData;
use App\Enums\UssdAction;
use App\Enums\UssdDataKey;
use App\Enums\UssdResponseType;
use App\Enums\UssdStepKey;
use App\Exceptions\DuplicateOptionKeysException;
use App\Exceptions\MissingOptionException;
use App\Http\Requests\UssdInteractionRequest;
use App\Http\Responses\SuccessResponse;
use App\Interfaces\UssdStepInterface;
use App\Repositories\UssdTransactionDataRepository;
use App\Ussd\Contracts\BaseStep;
use App\Ussd\Option;

class WelcomeStep extends BaseStep implements UssdStepInterface
{
    /**
     * @param UssdInteractionRequest $request
     * @param UssdTransactionDataRepository $ussdTransactionDataRepository
     * @param TitheMonthStep $titheMonthStep
     * @param PartnershipMonthStep $partnershipMonthStep
     * @param SeedStep $seedStep
     * @param OfferingStep $offeringStep
     *
     * @throws DuplicateOptionKeysException
     */
    public function __construct(
        protected UssdInteractionRequest $request,
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
        TitheMonthStep $titheMonthStep,
        PartnershipMonthStep $partnershipMonthStep,
        SeedStep $seedStep,
        OfferingStep $offeringStep,
    ) {
        $this->addOption(new Option(1, 'Tithe', $titheMonthStep))
            ->addOption(new Option(2, 'Partnership', $partnershipMonthStep))
            ->addOption(new Option(3, 'Seed', $seedStep))
            ->addOption(new Option(4, 'Offering', $offeringStep));
    }

    public function handle(UssdInteractionRequestData $requestDto, string|null $message = null, bool $replace = false): mixed
    {
        $options = $this->getOptions();
        $message = 'Welcome to Love Economy';
        foreach ($options as $option) {
            $message .= sprintf("\n%d. %s", $option->getKey(), $option->getDescription());
        }

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
        return UssdStepKey::WELCOME;
    }

    public function getSelectedOption(int $key): Option
    {
        $requestDto = $this->request->toDto();
        $txData = $this->ussdTransactionDataRepository->createEmptySessionData(sessionId: $requestDto->sessionId);

        $options = $this->getOptions();
        foreach ($options as $option) {
            if ($option->getKey() !== $key) {
                continue;
            }

            $this->ussdTransactionDataRepository->update(
                ussdTransactionData: $txData,
                data: [
                    'tx_data' => array_merge($txData['tx_data'], [
                        UssdDataKey::ACTION->value => $option->getNextStep()?->getAction()->value,
                    ]),
                ]
            );

            return $option;
        }

        throw new MissingOptionException(requestDto: $this->request->toDto(), message: 'Invalid option selected');
    }

    public function getAction(): UssdAction
    {
        return UssdAction::UNKNOWN;
    }
}
