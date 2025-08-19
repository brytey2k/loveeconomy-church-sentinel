<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\GivingType;
use App\Repositories\GivingTypeRepository;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param Request $request
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Share global props with all Inertia responses
        $givingTypes = resolve(GivingTypeRepository::class)
            ->allIndividual()
            ->map(static fn (GivingType $gt) => [
                'id' => $gt->id,
                'key' => $gt->key,
                'name' => $gt->name,
            ])->all();

        // Prepare authenticated user info and store in session if present
        $authUser = null;
        if ($request->user()) {
            $u = $request->user();
            // Build a sanitized user payload (avoid hidden/sensitive fields)
            $authUser = [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'branch_id' => $u->branch_id ?? null,
                'stationed_branch_id' => $u->stationed_branch_id ?? null,
            ];
        }

        return [
            ...parent::share($request),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            // Available in Vue via usePage().props.navGivingTypes
            'navGivingTypes' => $givingTypes,
            // Explicitly share the authenticated user
            'auth' => [
                'user' => $authUser,
            ],
        ];
    }
}
