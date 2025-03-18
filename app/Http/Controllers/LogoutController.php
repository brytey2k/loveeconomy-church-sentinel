<?php

namespace App\Http\Controllers;

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
