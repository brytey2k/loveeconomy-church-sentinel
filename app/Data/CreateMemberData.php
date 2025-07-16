<?php

declare(strict_types=1);

namespace App\Data;

class CreateMemberData
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
        ];
    }
}
