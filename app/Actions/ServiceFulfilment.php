<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dto\CreateMemberDto;
use App\Dto\CreateTransactionDto;
use App\Enums\PaymentProvider;
use App\Enums\TransactionState;
use App\Enums\TransactionType;
use App\Enums\UssdAction;
use App\Enums\UssdDataKey;
use App\Http\Requests\UssdFulfillmentRequest;
use App\Http\Responses\SuccessResponse;
use App\Models\Member;
use App\Models\UssdTransactionData;
use App\Repositories\MemberRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UssdTransactionDataRepository;
use App\Services\HubtelApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ServiceFulfilment
{
    private UssdFulfillmentRequest $request;
    public function __construct(
        protected MemberRepository $memberRepository,
        protected UssdTransactionDataRepository $ussdTransactionDataRepository,
        protected TransactionRepository $transactionRepository,
        protected HubtelApiClient $hubtelApiClient,
    ) {
    }

    public function execute(UssdFulfillmentRequest $request): JsonResponse
    {
        $this->setRequest($request);
        Log::info('Start processing webhook request', $request->toArray());

        $member = $this->createMemberIfNotExists();

        $sessionId = $request->string('SessionId')->toString();

        // todo: we will only proceed based on the response from here
        $response = $this->hubtelApiClient
            ->sendServiceFulfilmentRequest(sessionId: $sessionId, orderId: $request->string('OrderInfo.OrderId')->toString());

        // find the details and use it to create the transaction
        $txData = $this->ussdTransactionDataRepository->findBySessionId(sessionId: $sessionId);
        if ($txData === null) {
            Log::info('USSD transaction data does not exist. Creating transaction for missing session', [
                'session_id' => $sessionId,
            ]);

            $this->createTransactionForMissingSession(
                amount: $this->request->float('OrderInfo.Payment.AmountAfterCharges')
            );
        } else {
            Log::info('USSD transaction data retrieved', [
                'ussd_transaction_data' => $txData,
                'session_id' => $sessionId,
            ]);

            $this->createTransaction($member, $txData);
        }

        // todo: update this based on response from hubtel
        $txData->update(['status' => TransactionState::SUCCESSFUL]);

        Log::info('Finish processing webhook request', $request->toArray());

        return SuccessResponse::make(
            data: [
                'session_id' => $sessionId,
                'status' => 'success',
            ]
        );
    }

    private function setRequest(UssdFulfillmentRequest $request): void
    {
        $this->request = $request;
    }

    private function createTransactionForMissingSession(float $amount): void
    {
        Log::info('Creating transaction for missing session');

        $transaction = $this->transactionRepository->create(data: new CreateTransactionDto(
            memberId: 0,
            type: TransactionType::SEED,
            amount: $amount,
            status: TransactionState::SUCCESSFUL,
            paidThrough: PaymentProvider::HUBTEL,
            orderId: $this->request->string('OrderInfo.OrderId')->toString(),
            paymentType: $this->request->string('OrderInfo.PaymentType')->toString(),
            monthPaidFor: (int) now()->format('m'),
            yearPaidFor: (int) now()->format('Y'),
        ));

        Log::info('Missing session transaction created', [
            'transaction' => $transaction,
        ]);
    }

    private function createTransaction(Member $member, UssdTransactionData $ussdTxData): void
    {
        Log::info('Creating transaction for session', [
            'ussd_transaction_data' => $ussdTxData,
            'member' => $member,
        ]);

        $transaction = $this->transactionRepository->create(data: new CreateTransactionDto(
            memberId: $member->id,
            type: UssdAction::from($ussdTxData->tx_data[UssdDataKey::ACTION->value])->toTransactionType(),
            amount: (float) $ussdTxData->tx_data[UssdDataKey::AMOUNT->value],
            status: TransactionState::SUCCESSFUL,
            paidThrough: PaymentProvider::HUBTEL,
            orderId: $this->request->string('OrderInfo.OrderId')->toString(),
            paymentType: $this->request->string('OrderInfo.PaymentType')->toString(),
            monthPaidFor: (int) isset($ussdTxData->tx_data[UssdDataKey::MONTH->value])
                ? $ussdTxData->tx_data[UssdDataKey::MONTH->value]
                : now()->format('m'),
            yearPaidFor: (int) isset($ussdTxData->tx_data[UssdDataKey::YEAR->value])
                ? $ussdTxData->tx_data[UssdDataKey::YEAR->value]
                : now()->format('Y'),
        ));

        Log::info('Transaction created', [
            'transaction' => $transaction,
            'ussd_transaction_data' => $ussdTxData,
            'member' => $member,
        ]);
    }

    private function createMemberIfNotExists(): Member
    {
        $member = $this->memberRepository
            ->findByPhoneNumber(phoneNumber: $this->request->string('OrderInfo.CustomerMobileNumber')->toString());

        if ($member === null) {
            Log::info('Member not found, creating new member', [
                'phoneNumber' => $this->request->string('OrderInfo.CustomerMobileNumber')->toString()
            ]);

            $member = $this->memberRepository->create(data: new CreateMemberDto(
                firstName: $this->request->string('OrderInfo.CustomerName')->toString(),
                lastName: '',
                phone: $this->request->string('OrderInfo.CustomerMobileNumber')->toString(),
            ));

            Log::info('Member created', ['context' => $member]);
        }

        return $member;
    }
}
