<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UnauthenticatedResponse extends JsonResponse
{
    /**
     * @param array<string|int, mixed> $data
     * @param int $statusCode
     * @param array<string, mixed> $headers
     *
     * @return self
     */
    public static function make(
        array $data = [],
        int $statusCode = Response::HTTP_UNAUTHORIZED,
        array $headers = []
    ): self {
        return new self(
            data: $data,
            status: $statusCode,
            headers: $headers
        );
    }
}
