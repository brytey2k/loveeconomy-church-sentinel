<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Data\CreatePermissionData;
use App\Data\UpdatePermissionData;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionRepository
{
    /**
     * @return LengthAwarePaginator<Permission>
     */
    public function paginate(): LengthAwarePaginator
    {
        return Permission::orderBy('id')->paginate();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function all(): Collection
    {
        return Permission::all();
    }

    public function create(CreatePermissionData $permissionData): \Spatie\Permission\Contracts\Permission
    {
        return Permission::create($permissionData->toArray());
    }

    public function update(UpdatePermissionData $permissionData, Permission $permission): Permission
    {
        $permission->update($permissionData->toArray());

        return $permission;
    }
}
