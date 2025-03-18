<?php

declare(strict_types=1);

namespace App\Dto;

class CreateMemberDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone
    ) {
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
        ];
    }
}
