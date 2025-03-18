<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
use App\Services\UssdService;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UssdController extends Controller
{
    public function __construct(
        protected UssdService $ussdService,
        protected MemberRepository $memberRepository,
        protected TransactionRepository $transactionRepository,
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
        protected Repository $config
    ) {
    }

    /**
     * @param UssdInteractionRequest $request
     *
     * @return SuccessResponse
     *@throws MissingStepException
     *
     * @throws BindingResolutionException
     */
    public function interact(UssdInteractionRequest $request)
    {
        return $this->ussdService->processRequest($request->toDto());
    }

    public function fulfilment(UssdFulfillmentRequest $request)
    {
        Log::info('Start processing webhook request', $request->toArray());

        $member = $this->memberRepository
            ->findByPhoneNumber(phoneNumber: $request->string('OrderInfo.CustomerMobileNumber')->toString());

        if ($member === null) {
            Log::info('Member not found, creating new member', [
                'phoneNumber' => $request->string('OrderInfo.CustomerMobileNumber')->toString()
            ]);

            $member = $this->memberRepository->create(data: new CreateMemberDto(
                firstName: $request->string('OrderInfo.CustomerName')->toString(),
                lastName: '',
                phone: $request->string('OrderInfo.CustomerMobileNumber')->toString(),
            ));

            Log::info('Member created', ['context' => $member]);
        }

        $sessionId = $request->string('SessionId')->toString();

        // find the details and use it to create the transaction
        $txData = $this->ussdTransactionDataRepository->findBySessionId(sessionId: $sessionId);
        if ($txData === null) {
            // todo: throw an exception or something
        }

        Log::info('Transaction data retrieved', ['context' => $txData]);

        $this->transactionRepository->create(data: new CreateTransactionDto(
            memberId: $member->id,
            type: UssdAction::from($txData->tx_data[UssdDataKey::ACTION->value])->toTransactionType(),
            amount: (float) $txData->tx_data[UssdDataKey::AMOUNT->value],
            status: TransactionState::SUCCESSFUL,
            paidThrough: PaymentProvider::HUBTEL,
            orderId: $request->string('OrderInfo.OrderId')->toString(),
            paymentType: $request->string('OrderInfo.PaymentType')->toString(),
            monthPaidFor: (int) $txData->tx_data[UssdDataKey::MONTH->value] ?? now()->format('m'),
            yearPaidFor: (int) $txData->tx_data[UssdDataKey::YEAR->value] ?? now()->format('Y'),
        ));

        $request = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'Authorization' => sprintf(
                'Basic %s',
                base64_encode(
                    $this->config->get('services.hubtel.api_id') . ':' . $this->config->get('services.hubtel.api_key')
                )
            ),
        ])->withBody(json_encode([
            'SessionId' => $sessionId,
            'OrderId' => $request->string('OrderInfo.OrderId')->toString(),
            'ServiceStatus' => 'success',
            'MetaData' => null,
        ]));

        $response = $request->post($this->config->get('services.hubtel.fulfilment_url'));

        $recorded = Http::recorded()[0];
        var_dump($recorded);

        $txData->update(['status' => TransactionState::SUCCESSFUL]);

        return $response->json(); // this is returning null, why, todo: investigate
    }
}
