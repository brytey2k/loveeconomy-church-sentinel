<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\UssdInteractionRequestDto;
use App\Enums\UssdResponseType;
use App\Enums\UssdStepKey;
use App\Exceptions\MissingStepException;
use App\Http\Responses\SuccessResponse;
use App\Interfaces\UssdStepInterface;
use App\Repositories\UssdSessionRepository;
use App\Ussd\Steps\AmountStep;
use App\Ussd\Steps\BaseStep;
use App\Ussd\Steps\PartnershipMonthStep;
use App\Ussd\Steps\PartnershipYearStep;
use App\Ussd\Steps\SeedStep;
use App\Ussd\Steps\SendPaymentPromptStep;
use App\Ussd\Steps\TitheMonthStep;
use App\Ussd\Steps\TitheYearStep;
use App\Ussd\Steps\WelcomeStep;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;

class UssdService
{
    /**
     * @var array<string>
     */
    protected array $steps = [];

    public function __construct(
        protected UssdSessionRepository $ussdSessionRepository,
        WelcomeStep $welcomeStep,
        TitheMonthStep $titheMonthStep,
        TitheYearStep $titheYearStep,
        SeedStep $seedStep,
        SendPaymentPromptStep $sendPaymentPromptStep,
        AmountStep $amountStep,
        PartnershipMonthStep $partnershipMonthStep,
        PartnershipYearStep $partnershipYearStep
    ) {
        $this->registerStep($welcomeStep)
            ->registerStep($titheMonthStep)
            ->registerStep($titheYearStep)
            ->registerStep($seedStep)
            ->registerStep($sendPaymentPromptStep)
            ->registerStep($amountStep)
            ->registerStep($partnershipMonthStep)
            ->registerStep($partnershipYearStep);
        // Register other steps here
    }

    protected function registerStep(UssdStepInterface $step): static
    {
        $this->steps[$step->getKey()->value] = $step::class;
        return $this;
    }

    /**
     * @param UssdInteractionRequestDto $requestDto
     *
     * @return SuccessResponse
     *@throws MissingStepException
     *
     * @throws BindingResolutionException
     */
    public function processRequest(UssdInteractionRequestDto $requestDto): SuccessResponse
    {
        $this->ussdSessionRepository->create($requestDto);

        $clientState = $requestDto->clientState ?: UssdStepKey::WELCOME->value;

        // Determine the current step based on the clientState
        $steps = explode('/', $clientState);
        $currentStep = end($steps);

        // Handle the current step
        return $this->handleStep($currentStep, $requestDto);
    }

    /**
     * @param string $currentStep
     * @param UssdInteractionRequestDto $requestDto
     *
     * @return SuccessResponse
     *@throws MissingStepException
     *
     * @throws BindingResolutionException
     */
    protected function handleStep(string $currentStep, UssdInteractionRequestDto $requestDto): SuccessResponse
    {
        if (isset($this->steps[$currentStep])) {
            $stepClass = $this->steps[$currentStep];

            /** @var UssdStepInterface&BaseStep $stepInstance */
            $stepInstance = Application::getInstance()->make($stepClass);

            if ($stepInstance->isInitiationStep($requestDto)) {
                return $stepInstance->handle($requestDto);
            }

            if ($stepInstance->isTimeoutStep($requestDto)) {
                return $this->makeReleaseResponse($requestDto);
            }

            try {
                $option = $stepInstance->getSelectedOption((int) $requestDto->message);
            } catch (Exception $e) {
                report($e);
                return $stepInstance->handle($requestDto);
            }
            return $option->getNextStep()?->handle($requestDto) ?? $this->makeReleaseResponse($requestDto);
        }

        throw new MissingStepException(requestDto: $requestDto, message: 'Invalid step encountered');
    }

    protected function makeReleaseResponse(UssdInteractionRequestDto $requestDto): SuccessResponse
    {
        return SuccessResponse::make(
            data: [
                'SessionId' => $requestDto->sessionId,
                'Type' => UssdResponseType::RELEASE->value,
                'Message' => 'Session ended',
                'Label' => 'Goodbye',
                'ClientState' => '',
                'DataType' => 'display',
                'FieldType' => 'text',
            ],
            statusCode: 200,
            headers: ['Content-Type' => 'application/json']
        );
    }
}
