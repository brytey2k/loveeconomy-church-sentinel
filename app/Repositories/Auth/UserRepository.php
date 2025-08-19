<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Data\CreateUserData;
use App\Data\UpdateUserData;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): User|null
    {
        return User::where('email', $email)->first();
    }

    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginate(): LengthAwarePaginator
    {
        return User::orderBy('id')->paginate();
    }

    public function create(CreateUserData $createUserData): User
    {
        $data = $createUserData->toArray();
        if (!array_key_exists('stationed_branch_id', $data) || $data['stationed_branch_id'] === null) {
            $data['stationed_branch_id'] = $data['branch_id'];
        }
        return User::create($data);
    }

    public function update(User $user, UpdateUserData $updateUserData): bool
    {
        return $user->update($updateUserData->toArray());
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
