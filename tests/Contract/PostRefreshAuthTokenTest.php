<?php

declare(strict_types=1);

namespace Tests\Contract;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostRefreshAuthTokenTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_refreshes_token_successfully(): void
    {
        $this->freezeTime();

        $user = User::factory()->create();
        $user->createToken('access_token', ['*'], now()->addSeconds(config()->integer('auth.access_token_lifetime_in_seconds')))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addSeconds(config()->integer('auth.refresh_token_lifetime_in_seconds')))->plainTextToken;

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
        $response->assertJsonPath('expires_in', config()->integer('auth.access_token_lifetime_in_seconds'));
        $response->assertJsonPath('expires_at', now()->addSeconds(config()->integer('auth.access_token_lifetime_in_seconds'))->toDateTimeString());
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
        $user->createToken('access_token', [], now()->addSeconds(config()->integer('auth.access_token_lifetime_in_seconds')))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', [], now()->addSeconds(config()->integer('auth.refresh_token_lifetime_in_seconds')))->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$refreshToken}")
            ->postJson('/api/token/refresh');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'title',
                'detail',
            ]);
    }
}
