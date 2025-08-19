<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
        $transactions = Transaction::query()
            ->with(['member:id,first_name,last_name', 'givingType:id,name'])
            ->orderByDesc('tx_date')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('Payments/Index', [
            'transactions' => $transactions,
        ]);
    }
}
