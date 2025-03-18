<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Dto\UssdInteractionRequestDto;
use Exception;
use Throwable;

class MissingStepException extends Exception
{
    protected UssdInteractionRequestDto $requestDto;
    public function __construct(UssdInteractionRequestDto $requestDto, string $message = '', int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->requestDto = $requestDto;
    }

    public function getRequestDto(): UssdInteractionRequestDto
    {
        return $this->requestDto;
    }
}
