<?php

declare(strict_types=1);

use App\Enums\UssdResponseType;
use App\Exceptions\DuplicateOptionKeysException;
use App\Exceptions\MissingOptionException;
use App\Exceptions\MissingStepException;
use App\Exceptions\SentinelException;
use App\Http\Middleware\ForceAcceptHeader;
use App\Http\Responses\ErrorResponse;
use App\Http\Responses\SuccessResponse;
use App\Providers\UserRepositoryProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(static function (Middleware $middleware) {
        $middleware->alias(['force.accept-header' => ForceAcceptHeader::class]);
    })
    ->withProviders([
        UserRepositoryProvider::class,
    ])
    ->withExceptions(static function (Exceptions $exceptions) {
        $exceptions->render(static fn (SentinelException $exception) => ErrorResponse::fromException($exception));

        $exceptions->render(static fn (MissingStepException $exception) => SuccessResponse::make(
            data: [
                'SessionId' => $exception->getRequestDto()->sessionId,
                'Type' => UssdResponseType::RELEASE->value,
                'Message' => $exception->getMessage(),
                'Label' => 'Error',
                'ClientState' => '',
                'DataType' => 'display',
                'FieldType' => 'text',
            ],
            statusCode: 200,
            headers: ['Content-Type' => 'application/json']
        ));

        $exceptions->render(static fn (DuplicateOptionKeysException $exception, Request $request) => SuccessResponse::make(
            data: [
                'SessionId' => $request->input('SessionId'),
                'Type' => UssdResponseType::RELEASE->value,
                'Message' => $exception->getMessage(),
                'Label' => 'Error',
                'ClientState' => '',
                'DataType' => 'display',
                'FieldType' => 'text',
            ],
            statusCode: 200,
            headers: ['Content-Type' => 'application/json']
        ));

        $exceptions->render(static fn (MissingOptionException $exception, Request $request) => SuccessResponse::make(
            data: [
                'SessionId' => $request->input('SessionId'),
                'Type' => UssdResponseType::RELEASE->value,
                'Message' => $exception->getMessage(),
                'Label' => 'Error',
                'ClientState' => '',
                'DataType' => 'display',
                'FieldType' => 'text',
            ],
            statusCode: 200,
            headers: ['Content-Type' => 'application/json']
        ));
    })->create();
