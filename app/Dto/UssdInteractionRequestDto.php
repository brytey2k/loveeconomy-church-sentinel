<?php

declare(strict_types=1);

namespace App\Dto;

class UssdInteractionRequestDto
{
    /**
     * Create a new class instance.
     *
     * @param ?string $type
     * @param ?string $message
     * @param ?string $serviceCode
     * @param ?string $operator
     * @param ?string $clientState
     * @param ?string $mobile
     * @param ?string $sessionId
     * @param ?int $sequence
     * @param ?string $platform
     */
    public function __construct(
        public string|null $type,
        public string|null $message,
        public string|null $serviceCode,
        public string|null $operator,
        public string|null $clientState,
        public string|null $mobile,
        public string|null $sessionId,
        public int|null $sequence,
        public string|null $platform
    ) {
    }

    public function toArray(): array
    {
        return [
            'Type' => $this->type,
            'Message' => $this->message,
            'ServiceCode' => $this->serviceCode,
            'Operator' => $this->operator,
            'ClientState' => $this->clientState,
            'Mobile' => $this->mobile,
            'SessionId' => $this->sessionId,
            'Sequence' => $this->sequence,
            'Platform' => $this->platform
        ];
    }
}
