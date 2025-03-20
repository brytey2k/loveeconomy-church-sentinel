<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ServiceFulfilment;
use App\Dto\CreateMemberDto;
use App\Dto\CreateTransactionDto;
use App\Enums\PaymentProvider;
use App\Enums\TransactionState;
use App\Enums\UssdAction;
use App\Enums\UssdDataKey;
use App\Exceptions\MissingStepException;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * @return SuccessResponse
     * @throws MissingStepException
     *
     * @throws BindingResolutionException
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
