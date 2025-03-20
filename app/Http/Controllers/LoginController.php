<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApiLoginRequest;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\UnauthenticatedResponse;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(
        protected readonly UserRepositoryInterface $userRepository
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
        $accessToken = $user->createToken('access_token', [], now()->addSeconds(UserRepositoryInterface::ACCESS_TOKEN_LIFETIME_IN_SECONDS))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'], now()->addSeconds(UserRepositoryInterface::REFRESH_TOKEN_LIFETIME_IN_SECONDS))->plainTextToken;

        return SuccessResponse::make(data: [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 900, // 15 minutes
            'expires_at' => now()->addMinutes(15)->toDateTimeString(),
        ]);
    }
}
