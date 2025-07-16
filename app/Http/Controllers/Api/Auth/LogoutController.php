<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete(); // Revoke all tokens

        return SuccessResponse::make(data: [
            'title' => 'Logged Out',
            'details' => 'Logged out successfully',
        ]);
    }
}
