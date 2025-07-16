<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreatePermissionData;
use App\Data\UpdatePermissionData;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Repositories\PermissionRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PermissionsController extends Controller
{
    public function __construct(protected PermissionRepository $permissionRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Permissions/Index', [
            'permissions' => $this->permissionRepository->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Permissions/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePermissionData $permissionData
     */
    public function store(CreatePermissionData $permissionData): RedirectResponse
    {
        $this->permissionRepository->create($permissionData);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Permission $permission
     */
    public function edit(Permission $permission): Response
    {
        return Inertia::render('Permissions/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePermissionData $updatePermissionData
     * @param Permission $permission
     */
    public function update(UpdatePermissionData $updatePermissionData, Permission $permission): RedirectResponse
    {
        $this->permissionRepository->update($updatePermissionData, $permission);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }
}
