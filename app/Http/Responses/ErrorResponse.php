<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends JsonResponse
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
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $headers = []
    ): self {
        return new self(
            data: $data,
            status: $statusCode,
            headers: $headers
        );
    }

    /**
     * @param \Throwable $throwable
     * @param array<string, mixed> $headers
     *
     * @return ErrorResponse
     */
    public static function fromException(\Throwable $throwable, array $headers = []): ErrorResponse
    {
        return new self(
            data: [
                'message' => $throwable->getMessage()
            ],
            status: Response::HTTP_INTERNAL_SERVER_ERROR,
            headers: $headers
        );
    }
}
