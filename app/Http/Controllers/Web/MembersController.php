<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Data\CreateMemberData;
use App\Data\UpdateMemberData;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Repositories\MemberRepository;
use App\Repositories\PositionsRepository;
use App\Repositories\Structure\BranchesRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MembersController extends Controller
{
    public function __construct(
        protected MemberRepository $memberRepository,
        protected BranchesRepository $branchesRepository,
        protected PositionsRepository $positionsRepository,
        protected TagRepository $tagRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Members/Index', [
            'members' => $this->memberRepository->paginate(['branch', 'position', 'tags']),
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
            'tags' => $this->tagRepository->all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateMemberData $memberData
     */
    public function store(CreateMemberData $memberData): RedirectResponse
    {
        $this->memberRepository->create($memberData);

        return redirect()->route('members.index')->with('success', 'Member created successfully.');
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
