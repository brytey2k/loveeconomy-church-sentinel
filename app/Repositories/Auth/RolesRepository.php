<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Data\CreateRoleData;
use App\Data\UpdateRoleData;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RolesRepository
{
    /**
     * @return LengthAwarePaginator<Role>
     */
    public function paginate(): LengthAwarePaginator
    {
        return Role::paginate();
    }

    /**
     * @return Collection<int, Role>
     */
    public function all(): Collection
    {
        return Role::all();
    }

    public function create(CreateRoleData $data): Role
    {
        return Role::query()->create($data->toArray());
    }

    public function update(Role $role, UpdateRoleData $data): Role
    {
        $role->update($data->toArray());
        return $role;
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
