<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SuccessResponse extends JsonResponse
{
    public static function make(
        array $data = [],
        int $statusCode = Response::HTTP_OK,
        array $headers = []
    ): self {
        return new self(
            data: $data,
            status: $statusCode,
            headers: $headers
        );
    }
}
