<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateMemberData;
use App\Data\CreateMemberPaymentData;
use App\Data\UpdateMemberData;
use App\Enums\TransactionState;
use App\Http\Controllers\Controller;
use App\Models\FxRateHistory;
use App\Models\Member;
use App\Models\Transaction;
use App\Repositories\CurrencyRepository;
use App\Repositories\GivingTypeRepository;
use App\Repositories\MemberRepository;
use App\Repositories\PositionsRepository;
use App\Repositories\Structure\BranchesRepository;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

class MembersController extends Controller
{
    public function __construct(
        protected MemberRepository $memberRepository,
        protected BranchesRepository $branchesRepository,
        protected PositionsRepository $positionsRepository,
        protected GivingTypeRepository $tagRepository,
        protected CurrencyRepository $currencyRepository,
    ) {
    }

    /**
     * Store a newly created payment for the given member.
     *
     * @param CreateMemberPaymentData $data
     * @param Member $member
     */
    public function storePayment(CreateMemberPaymentData $data, Member $member): RedirectResponse
    {
        // Normalize currency code
        $currencyCode = strtoupper(trim($data->currency_code));
        $reportingCurrency = strtoupper(config()->string('fx.reporting_currency'));

        // Parse entered amount to minor units using moneyphp/money
        $currencies = new ISOCurrencies();
        $parser = new DecimalMoneyParser($currencies);
        $money = $parser->parse($data->amount, new Currency($currencyCode));
        // Money stores amount as integer of minor units in string form
        $amountRaw = (int) $money->getAmount();

        // Determine subunits for conversion
        $originSubunit = $currencies->subunitFor(new Currency($currencyCode));
        $reportSubunit = $currencies->subunitFor(new Currency($reportingCurrency));

        // Fetch current FX rate from DB (base: reporting -> quote: original currency), then invert for currency->reporting
        $fxRateToReporting = '1';
        if ($currencyCode !== $reportingCurrency) {
            $latest = FxRateHistory::query()
                ->where('base_currency', $reportingCurrency)
                ->where('quote_currency', $currencyCode)
                ->orderByDesc('as_of_hour')
                ->select(['rate'])
                ->first();

            if (!$latest) {
                return redirect()->back()->withErrors(['currency_code' => 'FX rate unavailable for selected currency.']);
            }

            // fxRateToReporting = 1 / (reporting->quote)
            $fxRateToReporting = BigDecimal::one()
                ->dividedBy(BigDecimal::of((string) $latest->rate), 10, RoundingMode::HALF_UP)
                ->toScale(10, RoundingMode::HALF_UP)
                ->__toString();
        } else {
            $fxRateToReporting = '1.0000000000';
        }

        // Compute converted_raw in reporting currency minor units without floating point errors
        $amountMinor = BigDecimal::of((string) $amountRaw);
        $originFactor = BigDecimal::of('10')->power($originSubunit);
        $reportFactor = BigDecimal::of('10')->power($reportSubunit);

        $amountMajor = $amountMinor->dividedBy($originFactor, 16, RoundingMode::DOWN);
        $amountRepMajor = $amountMajor->multipliedBy(BigDecimal::of($fxRateToReporting));
        $convertedMinor = $amountRepMajor->multipliedBy($reportFactor)->toScale(0, RoundingMode::HALF_UP);
        $convertedRaw = (int) (string) $convertedMinor;

        // Determine branch context (ID and currency)
        $user = auth()->user();
        $branchId = $user->branch_id;

        $branchCurrency = $user?->branch?->currency;
        if (!$branchCurrency) {
            return back()
                ->with('error', 'A branch currency must be set to be able to record payments.');
        }
        $branchCurrency = strtoupper($branchCurrency);
        $branchSubunit = $currencies->subunitFor(new Currency($branchCurrency));

        // Compute branch_converted_raw (minor units)
        if ($branchCurrency === $reportingCurrency) {
            $branchConvertedRaw = $convertedRaw;
        } elseif ($branchCurrency === $currencyCode) {
            $branchConvertedRaw = $amountRaw;
        } else {
            // Need reporting -> branch (R->B) rate from DB
            $latestRB = FxRateHistory::query()
                ->where('base_currency', $reportingCurrency)
                ->where('quote_currency', $branchCurrency)
                ->orderByDesc('as_of_hour')
                ->select(['rate'])
                ->first();

            if (!$latestRB) {
                return redirect()->back()->withErrors(['currency_code' => 'FX rate unavailable for reporting->branch conversion.']);
            }

            $fxCR = BigDecimal::of($fxRateToReporting); // C->R
            $fxRB = BigDecimal::of((string) $latestRB->rate); // R->B
            $fxCB = $fxCR->multipliedBy($fxRB); // C->B

            // amountMajor already computed; convert to branch minor units
            $branchFactor = BigDecimal::of('10')->power($branchSubunit);
            $amountBranchMajor = $amountMajor->multipliedBy($fxCB);
            $branchMinor = $amountBranchMajor->multipliedBy($branchFactor)->toScale(0, RoundingMode::HALF_UP);
            $branchConvertedRaw = (int) (string) $branchMinor;
        }

        // Persist transaction including branch snapshot
        Transaction::query()->create([
            'tx_date' => Carbon::parse($data->transaction_date)->startOfDay(),
            'member_id' => $member->id,
            'giving_type_id' => $data->giving_type_id,
            'giving_type_system_id' => $data->giving_type_system_id,
            'status' => TransactionState::SUCCESSFUL,
            'month_paid_for' => $data->month_paid_for,
            'year_paid_for' => $data->year_paid_for,
            'amount_raw' => $amountRaw,
            'currency' => $currencyCode,
            'fx_rate' => $fxRateToReporting,
            'reporting_currency' => $reportingCurrency,
            'converted_raw' => $convertedRaw,
            'branch_id' => $branchId ?: null,
            'branch_reporting_currency' => $branchCurrency,
            'branch_converted_raw' => $branchConvertedRaw,
            'original_amount_entered' => $data->amount,
            'original_amount_currency' => $currencyCode,
        ]);

        return redirect()->route('members.givings', ['member' => $member->id])
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show the form for creating a payment for a specific member.
     *
     * @param Member $member
     */
    public function createPayment(Member $member): RedirectResponse|Response
    {
        $member->load(['givingTypes:id,name,key', 'givingTypeSystems:id,name,giving_type_id']);

        // Prepare giving types
        $givingTypes = $member->givingTypes->map(static fn ($gt) => [
            'id' => $gt->id,
            'name' => $gt->name,
            'key' => $gt->key,
        ])->values();

        // Group assigned systems by giving type id
        $systemsByGivingType = [];
        foreach ($member->givingTypeSystems as $sys) {
            $systemsByGivingType[$sys->giving_type_id] ??= [];
            $systemsByGivingType[$sys->giving_type_id][] = [
                'id' => $sys->id,
                'name' => $sys->name,
            ];
        }

        // Build currency dropdown with FX rate to reporting currency
        $reportingCurrency = config()->string('fx.reporting_currency');
        $currencies = $this->currencyRepository->all();

        $currenciesWithRates = [];
        foreach ($currencies as $cur) {
            $code = $cur->short_name; // ISO code like USD, GHS
            $latest = null;
            $rawRate = null; // base (reporting) -> quote (currency)
            $asOf = null;

            if (strtoupper($code) === strtoupper($reportingCurrency)) {
                $rawRate = 1.0;
                $asOf = now();
            } else {
                $latest = FxRateHistory::query()
                    ->where('base_currency', strtoupper($reportingCurrency))
                    ->where('quote_currency', strtoupper($code))
                    ->orderByDesc('as_of_hour')
                    ->select(['rate', 'as_of_hour'])
                    ->first();

                if ($latest) {
                    $rawRate = (float) $latest->rate; // 1 reporting -> rawRate quote
                    $asOf = $latest->as_of_hour;
                }
            }

            // rate_to_reporting: how many reporting currency units per 1 unit of this currency
            $rateToReporting = null;
            if ($rawRate !== null && $rawRate > 0) {
                // If code === reporting, rateToReporting = 1
                $rateToReporting = strtoupper($code) === strtoupper($reportingCurrency)
                    ? 1.0
                    : 1.0 / $rawRate;
            }

            $currenciesWithRates[] = [
                'id' => $cur->id,
                'code' => $code,
                'name' => $cur->name,
                'symbol' => $cur->symbol,
                'rate_to_reporting' => $rateToReporting, // null if unavailable
                'base_reporting_to_quote_rate' => $rawRate, // raw DB direction
                'as_of_hour' => $asOf,
            ];
        }

        // Determine branch reporting currency for the current user
        $user = auth()->user();
        if ($user?->branch?->currency === null) {
            return back()->with('error', 'A branch currency must be set to be able to create payments.');
        }
        $branchCurrency = strtoupper($user?->branch?->currency);

        // Compute reporting -> branch rate (R->B) from DB for live preview
        $reportingToBranchRate = null;
        if ($branchCurrency === strtoupper($reportingCurrency)) {
            $reportingToBranchRate = 1.0;
        } else {
            $latestRB = FxRateHistory::query()
                ->where('base_currency', strtoupper($reportingCurrency))
                ->where('quote_currency', strtoupper($branchCurrency))
                ->orderByDesc('as_of_hour')
                ->select(['rate'])
                ->first();
            if ($latestRB) {
                // DB rate is base(R) -> quote(B)
                $reportingToBranchRate = (float) $latestRB->rate;
            }
        }

        return Inertia::render('Members/Payments/Create', [
            'member' => [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
            ],
            'givingTypes' => $givingTypes,
            'systemsByGivingType' => $systemsByGivingType,
            'reportingCurrency' => $reportingCurrency,
            'branchReportingCurrency' => $branchCurrency,
            'reportingToBranchRate' => $reportingToBranchRate,
            'currencies' => $currenciesWithRates,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $givingTypeId = request()->integer('giving_type_id');
        $givingTypeKey = request()->string('giving_type_key')->toString();

        $members = null;
        $filterGivingType = null;
        if ($givingTypeId || $givingTypeKey) {
            $members = $this->memberRepository->paginateByGivingType(
                $givingTypeId ?: null,
                $givingTypeKey ?: null,
                ['branch', 'position']
            );

            // Resolve the current giving type name for the UI title
            $gtQuery = \App\Models\GivingType::query();
            if ($givingTypeId) {
                $gt = $gtQuery->find($givingTypeId);
            } else {
                $gt = $gtQuery->where('key', $givingTypeKey)->first();
            }
            if ($gt) {
                $filterGivingType = [
                    'id' => $gt->id,
                    'key' => $gt->key,
                    'name' => $gt->name,
                ];
            }
        } else {
            $members = $this->memberRepository->paginate(['branch', 'position']);
        }

        return Inertia::render('Members/Index', [
            'members' => $members,
            'filterGivingType' => $filterGivingType,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Members/Create', [
            'branches' => $this->branchesRepository->all(),
            'positions' => $this->positionsRepository->all(),
            'tags' => $this->tagRepository->allIndividual(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateMemberData $memberData
     */
    public function store(CreateMemberData $memberData): RedirectResponse
    {
        $member = $this->memberRepository->create($memberData);

        // After creation and auto-assignments, always redirect to the member givings management page
        return redirect()
            ->route('members.givings', ['member' => $member->id])
            ->with('success', 'Member created successfully. You can now manage giving type systems.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Member $member
     */
    public function edit(Member $member): Response
    {
        $member->load('tags');
        return Inertia::render('Members/Edit', [
            'member' => $member,
            'branches' => $this->branchesRepository->all(),
            'positions' => $this->positionsRepository->all(),
            'tags' => $this->tagRepository->all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMemberData $updateMemberData
     * @param Member $member
     */
    public function update(UpdateMemberData $updateMemberData, Member $member): RedirectResponse
    {
        $this->memberRepository->update($member, $updateMemberData);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Member $member
     */
    public function destroy(Member $member): RedirectResponse
    {
        $this->memberRepository->delete($member);

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }
}
