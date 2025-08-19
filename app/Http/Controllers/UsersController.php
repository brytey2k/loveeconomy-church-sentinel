<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\CreateUserData;
use App\Data\UpdateUserData;
use App\Models\User;
use App\Repositories\Auth\UserRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => $this->userRepository->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Users/Create', [
            'branches' => \App\Models\Branch::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserData $createUserData
     */
    public function store(CreateUserData $createUserData): RedirectResponse
    {
        $this->userRepository->create($createUserData);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     */
    public function show(User $user): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     */
    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserData $updateUserData
     * @param User $user
     */
    public function update(UpdateUserData $updateUserData, User $user): RedirectResponse
    {
        $this->userRepository->update($user, $updateUserData);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userRepository->delete($user);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
