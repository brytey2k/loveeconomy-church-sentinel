<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Data\Ussd\UssdInteractionRequestData;
use Exception;
use Throwable;

class MissingStepException extends Exception
{
    protected UssdInteractionRequestData $requestDto;
    public function __construct(UssdInteractionRequestData $requestDto, string $message = '', int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->requestDto = $requestDto;
    }

    public function getRequestDto(): UssdInteractionRequestData
    {
        return $this->requestDto;
    }
}
