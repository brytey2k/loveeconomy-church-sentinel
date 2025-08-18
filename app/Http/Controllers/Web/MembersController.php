<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateMemberData;
use App\Data\UpdateMemberData;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Repositories\GivingTypeRepository;
use App\Repositories\MemberRepository;
use App\Repositories\PositionsRepository;
use App\Repositories\Structure\BranchesRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MembersController extends Controller
{
    public function __construct(
        protected MemberRepository $memberRepository,
        protected BranchesRepository $branchesRepository,
        protected PositionsRepository $positionsRepository,
        protected GivingTypeRepository $tagRepository,
    ) {
    }

    /**
     * Show the form for creating a payment for a specific member.
     */
    public function createPayment(Member $member): Response
    {
        $member->load(['givingTypes:id,name,key', 'givingTypeSystems:id,name,giving_type_id']);

        // Prepare giving types
        $givingTypes = $member->givingTypes->map(fn ($gt) => [
            'id' => $gt->id,
            'name' => $gt->name,
            'key' => $gt->key,
        ])->values();

        // Group assigned systems by giving type id
        $systemsByGivingType = [];
        foreach ($member->givingTypeSystems as $sys) {
            $systemsByGivingType[$sys->giving_type_id] = $systemsByGivingType[$sys->giving_type_id] ?? [];
            $systemsByGivingType[$sys->giving_type_id][] = [
                'id' => $sys->id,
                'name' => $sys->name,
            ];
        }

        return Inertia::render('Members/Payments/Create', [
            'member' => [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
            ],
            'givingTypes' => $givingTypes,
            'systemsByGivingType' => $systemsByGivingType,
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
