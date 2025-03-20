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
use App\Ussd\Contracts\BaseStep;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class UssdService
{
    /**
     * @var array<string>
     */
    protected array $steps = [];

    public function __construct(
        protected UssdSessionRepository $ussdSessionRepository,
    ) {
        $this->registerUssdStepClasses();
    }

    protected function registerUssdStepClasses(): void
    {
        $stepsPath = app_path('Ussd/Steps');
        $stepFiles = File::allFiles($stepsPath);
        $stepClasses = [];

        foreach ($stepFiles as $file) {
            $className = 'App\\Ussd\\Steps\\' . $file->getFilenameWithoutExtension();
            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                if ($reflection->implementsInterface('App\\Interfaces\\UssdStepInterface') && !$reflection->isAbstract()) {
                    $stepClasses[] = $className;
                }
            }
        }

        foreach ($stepClasses as $stepClass) {
            $this->steps[$stepClass::getKey()->value] = $stepClass;
        }
    }

    /**
     * @param UssdInteractionRequestDto $requestDto
     *
     * @throws MissingStepException
     * @throws BindingResolutionException
     *
     * @return SuccessResponse
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
     * @throws MissingStepException
     * @throws BindingResolutionException
     *
     * @return SuccessResponse
     */
    protected function handleStep(string $currentStep, UssdInteractionRequestDto $requestDto): SuccessResponse
    {
        if (isset($this->steps[$currentStep])) {
            $stepClass = $this->steps[$currentStep];

            Log::info('Handling step', ['step' => $stepClass]);

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
