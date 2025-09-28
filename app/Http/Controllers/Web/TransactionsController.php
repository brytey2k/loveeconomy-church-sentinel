<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GivingType;
use App\Models\GivingTypeSystem;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

class TransactionsController extends Controller
{
    /**
     * Display a listing of payments keyed into the system.
     */
    public function index(): Response
    {
        $givingTypeId = request()->integer('giving_type_id');
        $givingTypeSystemId = request()->integer('giving_type_system_id');

        $query = Transaction::query()
            ->with([
                'member:id,first_name,last_name',
                'givingType:id,name',
                'givingTypeSystem:id,name',
            ]);

        if ($givingTypeId) {
            $query->where('giving_type_id', $givingTypeId);
        }

        if ($givingTypeSystemId) {
            $query->where('giving_type_system_id', $givingTypeSystemId);
        }

        $transactions = $query
            ->orderByDesc('tx_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // Build dropdown data
        $givingTypes = GivingType::query()
            ->orderBy('name')
            ->get(['id', 'name', 'key']);

        $systems = GivingTypeSystem::query()
            ->orderBy('name')
            ->get(['id', 'name', 'giving_type_id']);

        $systemsByGivingType = [];
        foreach ($systems as $sys) {
            $systemsByGivingType[$sys->giving_type_id] ??= [];
            $systemsByGivingType[$sys->giving_type_id][] = [
                'id' => $sys->id,
                'name' => $sys->name,
            ];
        }

        return Inertia::render('Payments/Index', [
            'transactions' => $transactions,
            'givingTypes' => $givingTypes,
            'systemsByGivingType' => $systemsByGivingType,
            'filters' => [
                'giving_type_id' => $givingTypeId,
                'giving_type_system_id' => $givingTypeSystemId,
            ],
        ]);
    }
}
