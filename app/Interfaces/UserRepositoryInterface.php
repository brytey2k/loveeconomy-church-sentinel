<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public const ACCESS_TOKEN_LIFETIME_IN_SECONDS = 15 * 60; // 15 minutes
    public const REFRESH_TOKEN_LIFETIME_IN_SECONDS = 7 * 24 * 60 * 60; // 7 days
    public function findByEmail(string $email): User|null;
}
