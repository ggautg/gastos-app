<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Inertia\Inertia;

/**
 * Panel principal con el resumen del hogar activo del mes corriente.
 */
class DashboardController extends Controller
{
    /**
     * Muestra gastos, ganancias, balance y totales por día del mes actual del hogar.
     */
    public function index()
    {
        $household = $this->currentHousehold();

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
            'household' => $household->only('id', 'name'),
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