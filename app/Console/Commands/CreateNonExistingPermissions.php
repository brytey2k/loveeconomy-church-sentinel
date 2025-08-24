<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Data\CreatePermissionData;
use App\Enums\Permission;
use App\Repositories\PermissionRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Spatie\Permission\PermissionRegistrar;

class CreateNonExistingPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-non-existing-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create non-existing permissions';

    /**
     * Execute the console command.
     *
     * @throws BindingResolutionException
     */
    public function handle(PermissionRepository $permissionRepository): void
    {
        $this->info('Creating non-existing permissions...');

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permission::cases() as $permission) {
            $this->info("Find or create permission: {$permission->value}");
            $permissionRepository->create(new CreatePermissionData(name: $permission->value));
        }

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Done.');
    }
}
