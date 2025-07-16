<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateRoleData;
use App\Data\UpdateRoleData;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Repositories\Auth\RolesRepository;
use App\Repositories\PermissionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RolesController extends Controller
{
    public function __construct(
        protected readonly RolesRepository $rolesRepository,
        protected readonly PermissionRepository $permissionRepository,
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('Roles/Index', [
            'roles' => $this->rolesRepository->paginate(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create');
    }

    public function store(CreateRoleData $roleData): RedirectResponse
    {
        $this->rolesRepository->create($roleData);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', [
            'role' => $role,
        ]);
    }

    public function update(Role $role, UpdateRoleData $updateRoleData): RedirectResponse
    {
        $this->rolesRepository->update(role: $role, data: $updateRoleData);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->rolesRepository->delete($role);

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function permissionsForm(Role $role): Response
    {
        return Inertia::render('Roles/AddPermissionsToRole', [
            'role' => $role,
            'all_permissions' => $this->permissionRepository->all(),
            'role_permissions' => $role->permissions,
        ]);
    }

    public function savePermissions(Role $role, Request $request): RedirectResponse
    {
        $role->syncPermissions($request->array('permissions'));

        return redirect()->route('roles.permissions-for', $role)
            ->with('success', 'Permissions updated successfully.');
    }
}
