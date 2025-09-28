<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * System permissions as a string-backed enum.
 *
 */
enum Permission: string
{
    case ViewBranches = 'view branches';
    case CreateBranches = 'create branches';
    case ManageBranches = 'manage branches';

    case ViewUsers = 'view users';
    case CreateUsers = 'create users';
    case ManageUsers = 'manage users';

    case ViewRoles = 'view roles';
    case CreateRoles = 'create roles';
    case ManageRoles = 'manage roles';

    case ViewPermissions = 'view permissions';
    case CreatePermissions = 'create permissions';
    case ManagePermissions = 'manage permissions';

    case CreateLevels = 'create levels';
    case ViewLevels = 'view levels';
    case ManageLevels = 'manage levels';

    case CreateCountries = 'create countries';
    case ViewCountries = 'view countries';
    case ManageCountries = 'manage countries';

    case CreateMembers = 'create members';
    case ViewMembers = 'view members';
    case ManageMembers = 'manage members';

    case CreatePositions = 'create positions';
    case ViewPositions = 'view positions';
    case ManagePositions = 'manage positions';

    case ManageGivingTypes = 'manage giving types';

    case CreateTransactions = 'create transactions';
    case ViewTransactions = 'view transactions';
    case ManageTransactions = 'manage transactions';

    // Settings
    case ViewSettings = 'view settings';
    case ManageSettings = 'manage settings';
}


