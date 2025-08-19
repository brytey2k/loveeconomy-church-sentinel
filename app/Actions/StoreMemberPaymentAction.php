<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateMemberPaymentData;
use App\Data\Ussd\CreateTransactionData;
use App\Enums\TransactionState;
use App\Models\Member;
use App\Repositories\Finance\FxRatesHistoryRepository;
use App\Repositories\TransactionRepository;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Support\Carbon;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

class StoreMemberPaymentAction
{
    public function __construct(
        protected FxRatesHistoryRepository $fxRatesHistoryRepository,
        protected TransactionRepository $transactionRepository,
    ) {
    }

    /**
     * Execute the store payment logic previously in MembersController::storePayment.
     * Returns a consistent structured array: ['success' => bool, 'message' => string, 'data' => array].
     *
     * @param CreateMemberPaymentData $data
     * @param Member $member
     *
     * @return array{success:bool,message?:string,data?:array<string,mixed>}
     */
    public function execute(CreateMemberPaymentData $data, Member $member): array
    {
        $currencyCodeKeyed = $this->normalizeCurrencyCode($data->currency_code);
        $reportingCurrencyCode = $this->getReportingCurrency();

        [$currencies, $parser] = $this->createParsers();

        $amountRaw = $this->parseAmountRaw($parser, $data->amount, $currencyCodeKeyed);

        $originSubunit = $this->getSubunit($currencies, $currencyCodeKeyed);
        $reportSubunit = $this->getSubunit($currencies, $reportingCurrencyCode);

        $fxRateToReporting = $this->computeFxRateToReporting($currencyCodeKeyed, $reportingCurrencyCode);
        if ($fxRateToReporting === null) {
            return $this->buildError('FX rate unavailable for selected currency.');
        }

        $amountMajor = $this->toMajorUnits($amountRaw, $originSubunit);
        $convertedRaw = $this->toConvertedRaw($amountMajor, $reportSubunit, $fxRateToReporting);

        $branchContext = $this->resolveBranchContext($currencies);
        if ($branchContext === null) {
            return $this->buildError('A branch currency must be set to be able to record payments.');
        }

        $branchConvertedRaw = $this->computeBranchConvertedRaw(
            amountMajor: $amountMajor,
            convertedRaw: $convertedRaw,
            amountRaw: $amountRaw,
            originalCurrency: $currencyCodeKeyed,
            reportingCurrency: $reportingCurrencyCode,
            branchCurrency: $branchContext['branchCurrency'],
            branchSubunit: $branchContext['branchSubunit'],
            fxRateToReporting: $fxRateToReporting,
        );
        if ($branchConvertedRaw === null) {
            return $this->buildError('FX rate unavailable for reporting->branch conversion.');
        }

        $this->persistTransaction(
            member: $member,
            data: $data,
            amountRaw: $amountRaw,
            currencyCode: $currencyCodeKeyed,
            fxRateToReporting: $fxRateToReporting,
            reportingCurrency: $reportingCurrencyCode,
            convertedRaw: $convertedRaw,
            branchId: $branchContext['branchId'],
            branchCurrency: $branchContext['branchCurrency'],
            branchConvertedRaw: $branchConvertedRaw,
        );

        return $this->buildSuccess(
            memberId: $member->id,
            reportingCurrency: $reportingCurrencyCode,
            fxRateToReporting: $fxRateToReporting,
        );
    }

    private function normalizeCurrencyCode(string $code): string
    {
        return strtoupper(trim($code));
    }

    private function getReportingCurrency(): string
    {
        return strtoupper(config()->string('fx.reporting_currency'));
    }

    /**
     * @return array{0: ISOCurrencies, 1: DecimalMoneyParser}
     */
    private function createParsers(): array
    {
        $currencies = new ISOCurrencies();
        return [$currencies, new DecimalMoneyParser($currencies)];
    }

    private function parseAmountRaw(DecimalMoneyParser $parser, string $amount, string $currencyCode): int
    {
        $money = $parser->parse($amount, new Currency($currencyCode));
        return (int) $money->getAmount();
    }

    private function getSubunit(ISOCurrencies $currencies, string $currency): int
    {
        return $currencies->subunitFor(new Currency($currency));
    }

    private function computeFxRateToReporting(string $currencyCode, string $reportingCurrencyCode): string|null
    {
        if ($currencyCode === $reportingCurrencyCode) {
            return '1.0000000000';
        }

        $latestRate = $this->fxRatesHistoryRepository->getLatestRate($reportingCurrencyCode, $currencyCode);
        if ($latestRate === null) {
            return null;
        }

        return BigDecimal::one()
            ->dividedBy(BigDecimal::of((string) $latestRate), 10, RoundingMode::HALF_UP)
            ->toScale(10, RoundingMode::HALF_UP)
            ->__toString();
    }

    private function toMajorUnits(int $amountRaw, int $originSubunit): BigDecimal
    {
        $amountMinor = BigDecimal::of((string) $amountRaw);
        $originFactor = BigDecimal::of('10')->power($originSubunit);
        return $amountMinor->dividedBy($originFactor, 16, RoundingMode::DOWN);
    }

    private function toConvertedRaw(BigDecimal $amountMajor, int $reportSubunit, string $fxRateToReporting): int
    {
        $amountRepMajor = $amountMajor->multipliedBy(BigDecimal::of($fxRateToReporting));
        $reportFactor = BigDecimal::of('10')->power($reportSubunit);
        $convertedMinor = $amountRepMajor->multipliedBy($reportFactor)->toScale(0, RoundingMode::HALF_UP);
        return (int) (string) $convertedMinor;
    }

    /**
     * @param ISOCurrencies $currencies
     *
     * @return array{branchId: int|null, branchCurrency: string, branchSubunit: int}|null
     */
    private function resolveBranchContext(ISOCurrencies $currencies): array|null
    {
        $user = auth()->user();
        $branchId = $user->branch_id;
        $branchCurrency = $user?->branch?->currency;
        if (!$branchCurrency) {
            return null;
        }
        $branchCurrency = strtoupper($branchCurrency);
        $branchSubunit = $this->getSubunit($currencies, $branchCurrency);

        return [
            'branchId' => $branchId ?: null,
            'branchCurrency' => $branchCurrency,
            'branchSubunit' => $branchSubunit,
        ];
    }

    private function computeBranchConvertedRaw(
        BigDecimal $amountMajor,
        int $convertedRaw,
        int $amountRaw,
        string $originalCurrency,
        string $reportingCurrency,
        string $branchCurrency,
        int $branchSubunit,
        string $fxRateToReporting,
    ): int|null {
        if ($branchCurrency === $reportingCurrency) {
            return $convertedRaw;
        }
        if ($branchCurrency === $originalCurrency) {
            return $amountRaw;
        }

        $latestRBRate = $this->fxRatesHistoryRepository->getLatestRate($reportingCurrency, $branchCurrency);
        if ($latestRBRate === null) {
            return null;
        }

        $fxCR = BigDecimal::of($fxRateToReporting); // C->R
        $fxRB = BigDecimal::of((string) $latestRBRate); // R->B
        $fxCB = $fxCR->multipliedBy($fxRB); // C->B

        $branchFactor = BigDecimal::of('10')->power($branchSubunit);
        $amountBranchMajor = $amountMajor->multipliedBy($fxCB);
        $branchMinor = $amountBranchMajor->multipliedBy($branchFactor)->toScale(0, RoundingMode::HALF_UP);
        return (int) (string) $branchMinor;
    }

    private function persistTransaction(
        Member $member,
        CreateMemberPaymentData $data,
        int $amountRaw,
        string $currencyCode,
        string $fxRateToReporting,
        string $reportingCurrency,
        int $convertedRaw,
        int|null $branchId,
        string $branchCurrency,
        int $branchConvertedRaw,
    ): void {
        $this->transactionRepository->create(new CreateTransactionData(
            memberId: $member->id,
            status: TransactionState::SUCCESSFUL,
            monthPaidFor: $data->month_paid_for,
            yearPaidFor: $data->year_paid_for,
            txDate: Carbon::parse($data->transaction_date)->startOfDay(),
            givingTypeId: $data->giving_type_id,
            givingTypeSystemId: $data->giving_type_system_id,
            amountRaw: $amountRaw,
            currency: $currencyCode,
            fxRate: $fxRateToReporting,
            reportingCurrency: $reportingCurrency,
            convertedRaw: $convertedRaw,
            branchId: $branchId,
            branchReportingCurrency: $branchCurrency,
            branchConvertedRaw: $branchConvertedRaw,
            originalAmountEntered: $data->amount,
            originalAmountCurrency: $currencyCode,
        ));
    }

    private function buildError(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => ['field' => 'currency_code'],
        ];
    }

    private function buildSuccess(int $memberId, string $reportingCurrency, string $fxRateToReporting): array
    {
        return [
            'success' => true,
            'message' => 'Payment recorded successfully.',
            'data' => [
                'member_id' => $memberId,
                'reporting_currency' => $reportingCurrency,
                'fx_rate_to_reporting' => $fxRateToReporting,
            ],
        ];
    }
}
