<?php

declare(strict_types=1);

namespace Tests\Contract;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostRefreshAuthTokenTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_refreshes_token_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a refresh token for the user
        $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addDays(7))->plainTextToken;

        // Store refresh token expiry in Redis
        Cache::put("refresh_token:{$refreshToken}", now()->addDays(7), now()->addDays(7));

        // Make a request to the refresh token endpoint
        $response = $this->withHeader('Authorization', "Bearer {$refreshToken}")
            ->postJson('/api/token/refresh');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'expires_in',
                'expires_at',
                'refresh_token',
            ]);
    }
}
