<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Resumen anual de ganancias y gastos del hogar, mes por mes.
 */
class YearlySummaryController extends Controller
{
    /**
     * Muestra, para cada mes del año elegido, el total de ganancias y gastos del hogar.
     */
    public function index(Request $request)
    {
        $household = $this->currentHousehold();
        $year = $this->year($request);

        $transacciones = Transaction::whereHas('category', function ($q) use ($household) {
            $q->where('household_id', $household->id);
        })
            ->whereYear('date', $year)
            ->get();

        $meses = collect(range(1, 12))->map(function ($mes) use ($transacciones) {
            $delMes = $transacciones->filter(fn ($t) => $t->date->month === $mes);

            return [
                'mes' => $mes,
                'ganancias' => $delMes->where('type', 'ganancia')->sum('amount_gs'),
                'gastos' => $delMes->where('type', 'gasto')->sum('amount_gs'),
            ];
        });

        return Inertia::render('Summary/Yearly', [
            'year' => $year,
            'meses' => $meses,
        ]);
    }
}
