<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class YearlySummaryController extends Controller
{
    public function index(Request $request)
    {
        $household = auth()->user()->currentHousehold();
        $year = $request->integer('year', now()->year);

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
