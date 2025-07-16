<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthenticatedResponse;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(
        protected readonly UserRepositoryInterface $userRepository,
        protected readonly Repository $config,
    ) {
    }
    public function __invoke(ApiLoginRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return UnauthenticatedResponse::make(data: [
                'title' => 'Unauthorized',
                'detail' => 'Invalid credentials'
            ]);
        }

        // Generate tokens
        $accessToken = $user->createToken('access_token', [], now()->addSeconds($this->config->integer('auth.access_token_lifetime_in_seconds')))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addSeconds(config()->integer('auth.refresh_token_lifetime_in_seconds')))->plainTextToken;

        return SuccessResponse::make(data: [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $this->config->integer('auth.access_token_lifetime_in_seconds'),
            'expires_at' => now()->addMinutes($this->config->integer('auth.access_token_lifetime_in_seconds'))->toDateTimeString(),
        ]);
    }
}
