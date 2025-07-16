<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ServiceFulfilment;
use App\Exceptions\MissingStepException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UssdFulfillmentRequest;
use App\Http\Requests\UssdInteractionRequest;
use App\Http\Responses\SuccessResponse;
use App\Repositories\MemberRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UssdTransactionDataRepository;
use App\Services\HubtelApiClient;
use App\Services\UssdService;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;

class UssdController extends Controller
{
    public function __construct(
        protected UssdService $ussdService,
        protected MemberRepository $memberRepository,
        protected TransactionRepository $transactionRepository,
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
        protected Repository $config,
        protected HubtelApiClient $hubtelApiClient,
    ) {
    }

    /**
     * @param UssdInteractionRequest $request
     *
     * @throws MissingStepException
     * @throws BindingResolutionException
     *
     * @return SuccessResponse
     */
    public function interact(UssdInteractionRequest $request): JsonResponse
    {
        return $this->ussdService->processRequest($request->toDto());
    }

    public function fulfilment(UssdFulfillmentRequest $request, ServiceFulfilment $serviceFulfilment): JsonResponse
    {
        return $serviceFulfilment->execute($request);
    }
}
