<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthenticatedResponse;
use App\Models\User;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class RefreshTokenController extends Controller
{
    public function __construct(
        protected readonly Repository $config,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $refreshToken = PersonalAccessToken::findToken($request->bearerToken());

        if ($refreshToken === null) {
            return UnauthenticatedResponse::make(data: [
                'title' => 'Unauthorized',
                'detail' => 'Invalid refresh token'
            ]);
        }

        if ($refreshToken->name !== 'refresh_token' || $refreshToken->cant('refresh')) {
            return UnauthenticatedResponse::make(data: [
                'title' => 'Unauthorized',
                'detail' => 'Invalid refresh token'
            ]);
        }

        if ($refreshToken->expires_at->isPast()) {
            return UnauthenticatedResponse::make(data: [
                'title' => 'Unauthorized',
                'detail' => 'Session expired. Please log in again.'
            ]);
        }

        // Extend refresh token expiry (sliding expiry)
        $refreshToken->update([
            'expires_at' => now()->addDays(7)
        ]);

        // Issue new access token
        /** @var User $user */
        $user = $refreshToken->tokenable;
        $newAccessToken = $user->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;

        return SuccessResponse::make(data: [
            'access_token' => $newAccessToken,
            'expires_in' => $this->config->integer('auth.access_token_lifetime_in_seconds'),
            'expires_at' => now()->addSeconds($this->config->integer('auth.access_token_lifetime_in_seconds'))->toDateTimeString(),
            'refresh_token' => $request->bearerToken(),
        ]);
    }
}
