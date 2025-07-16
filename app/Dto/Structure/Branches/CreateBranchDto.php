<?php

declare(strict_types=1);

namespace App\Dto\Structure\Branches;

class CreateBranchDto
{
    public function __construct(
        public string $name,
        public int $levelId,
        public int $countryId,
        public int|null $parentId = null,
    ) {
    }

    /**
     * @return array<string, string|int|null>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'level_id' => $this->levelId,
            'country_id' => $this->countryId,
            'parent_id' => $this->parentId,
            'path' => '',
        ];
    }
}
