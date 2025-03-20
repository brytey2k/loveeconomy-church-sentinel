<?php

declare(strict_types=1);

namespace Tests\Contract;

use App\Interfaces\UserRepositoryInterface;
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
        $this->freezeTime();

        $user = User::factory()->create();
        $user->createToken('access_token', ['*'], now()->addSeconds(UserRepositoryInterface::ACCESS_TOKEN_LIFETIME_IN_SECONDS))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addSeconds(UserRepositoryInterface::REFRESH_TOKEN_LIFETIME_IN_SECONDS))->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$refreshToken}")
            ->postJson('/api/token/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'expires_in',
                'expires_at',
                'refresh_token',
            ]);

        $response->assertJsonPath('refresh_token', $refreshToken);
        $response->assertJsonPath('expires_in', UserRepositoryInterface::ACCESS_TOKEN_LIFETIME_IN_SECONDS);
        $response->assertJsonPath('expires_at', now()->addSeconds(UserRepositoryInterface::ACCESS_TOKEN_LIFETIME_IN_SECONDS)->toDateTimeString());
    }

    #[Test]
    public function it_fails_to_refresh_when_refresh_token_is_not_found(): void
    {
        $accessToken = 'invalid_refresh_token';

        $response = $this->withHeader('Authorization', "Bearer {$accessToken}")
            ->postJson('/api/token/refresh');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'title',
                'detail',
            ]);
    }

    #[Test]
    public function it_fails_to_refresh_when_token_is_not_to_be_refreshed(): void
    {
        $user = User::factory()->create();
        $user->createToken('access_token', [], now()->addSeconds(UserRepositoryInterface::ACCESS_TOKEN_LIFETIME_IN_SECONDS))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', [], now()->addSeconds(UserRepositoryInterface::REFRESH_TOKEN_LIFETIME_IN_SECONDS))->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$refreshToken}")
            ->postJson('/api/token/refresh');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'title',
                'detail',
            ]);
    }
}
