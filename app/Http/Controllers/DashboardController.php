<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $household = Auth::user()->currentHousehold();

        $transactions = Transaction::whereHas('category', function ($q) use ($household) {
                $q->where('household_id', $household->id);
            })
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $totalGastos = $transactions->where('type', 'gasto')->sum('amount_gs');
        $totalGanancias = $transactions->where('type', 'ganancia')->sum('amount_gs');

        // Totales por día del mes actual, para la tarjeta de gasto diario.
        $dias = $transactions
            ->groupBy(fn ($transaction) => $transaction->date->format('Y-m-d'))
            ->map(fn ($items) => [
                'gastos' => (int) $items->where('type', 'gasto')->sum('amount_gs'),
                'ganancias' => (int) $items->where('type', 'ganancia')->sum('amount_gs'),
                'movimientos' => $items->count(),
            ]);

        return Inertia::render('Dashboard', [
            'household' => $household,
            'resumen' => [
                'gastos' => $totalGastos,
                'ganancias' => $totalGanancias,
                'balance' => $totalGanancias - $totalGastos,
                'movimientos' => $transactions->count(),
                'dias' => (object) $dias->toArray(),
                'hoy' => now()->toDateString(),
                'month' => now()->month,
                'year' => now()->year,
            ],
        ]);
    }
}