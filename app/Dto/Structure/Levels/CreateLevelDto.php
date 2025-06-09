<?php

declare(strict_types=1);

namespace App\Dto\Structure\Levels;

class CreateLevelDto
{
    public function __construct(
        public string $name,
        public int $position,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'position' => $this->position,
        ];
    }
}
