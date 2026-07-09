<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class YearlySummaryController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->integer('year', now()->year);

        $transacciones = auth()->user()->transactions()
            ->whereYear('date', $year)
            ->get();

        $meses = collect(range(1, 12))->map(function ($mes) use ($transacciones) {
            $delMes = $transacciones->filter(
                fn ($t) => $t->date->month === $mes
            );

            return [
                'mes' => $mes,
                'ganancias' => $delMes->where('type', 'ganancia')->sum('amount'),
                'gastos' => $delMes->where('type', 'gasto')->sum('amount'),
            ];
        });

        return Inertia::render('Summary/Yearly', [
            'year' => $year,
            'meses' => $meses,
        ]);
    }
}