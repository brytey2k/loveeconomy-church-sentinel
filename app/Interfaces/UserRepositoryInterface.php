<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Data\CreateUserData;
use App\Data\UpdateUserData;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): User|null;

    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginate(): LengthAwarePaginator;

    public function create(CreateUserData $createUserData): User;

    public function update(User $user, UpdateUserData $updateUserData): bool;

    public function delete(User $user): bool;
}
