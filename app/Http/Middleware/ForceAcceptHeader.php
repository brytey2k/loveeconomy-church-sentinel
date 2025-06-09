<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceAcceptHeader
{
    protected string $contentType;

    public function __construct(string $contentType = 'application/json')
    {
        $this->contentType = $contentType;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', $this->contentType);

        return $next($request);
    }
}
