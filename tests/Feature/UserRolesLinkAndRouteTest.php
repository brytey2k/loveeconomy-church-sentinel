<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserRolesLinkAndRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_open_add_roles_form_for_user(): void
    {
        $admin = User::factory()->create();
        // give permission needed for visibility/access if gate is applied later
        // For now, just acting as any authenticated user to access the route
        $user = User::factory()->create();

        // Ensure at least one role exists
        Role::findOrCreate('Tester');

        $response = $this->actingAs($admin)->get("/users/{$user->id}/roles");

        $response->assertStatus(200);
        $response->assertInertia(
            static fn (Assert $page) => $page
                ->component('Users/AddRolesToUser')
                ->where('user.id', $user->id)
        );
    }
}
