<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

/**
 * Análisis de la variación de gastos por categoría frente al mes anterior.
 */
class InsightsController extends Controller
{
    /**
     * Muestra el total del mes y la variación por categoría contra el mes anterior, ordenada por cambio.
     */
    public function index(Request $request)
    {
        $household = $this->currentHousehold();
        $month = $this->month($request);
        $year = $this->year($request);

        $fechaAnterior = Carbon::createFromDate($year, $month, 1)->subMonth();

        $transaccionesActual = Transaction::whereHas('category', function ($q) use ($household) {
                $q->where('household_id', $household->id);
            })
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'gasto')
            ->get();

        $transaccionesAnterior = Transaction::whereHas('category', function ($q) use ($household) {
                $q->where('household_id', $household->id);
            })
            ->whereMonth('date', $fechaAnterior->month)
            ->whereYear('date', $fechaAnterior->year)
            ->where('type', 'gasto')
            ->get();

        $totalesActual = $transaccionesActual->groupBy('category_id')->map(fn ($items) => $items->sum('amount_gs'));
        $totalesAnterior = $transaccionesAnterior->groupBy('category_id')->map(fn ($items) => $items->sum('amount_gs'));

        $categorias = $household->categories()->where('type', 'gasto')->get()->keyBy('id');

        $comparaciones = $totalesActual->keys()
            ->merge($totalesAnterior->keys())
            ->unique()
            ->map(function ($categoriaId) use ($totalesActual, $totalesAnterior, $categorias) {
                $actual = $totalesActual->get($categoriaId, 0);
                $anterior = $totalesAnterior->get($categoriaId, 0);

                if ($anterior <= 0) {
                    return null;
                }

                return [
                    'nombre' => $categorias->get($categoriaId)?->name ?? 'Categoría',
                    'actual' => $actual,
                    'anterior' => $anterior,
                    'porcentaje' => round((($actual - $anterior) / $anterior) * 100),
                ];
            })
            ->filter()
            ->sortByDesc(fn ($c) => abs($c['porcentaje']))
            ->values();

        return Inertia::render('Insights/Index', [
            'month' => $month,
            'year' => $year,
            'totalActual' => $transaccionesActual->sum('amount_gs'),
            'totalAnterior' => $transaccionesAnterior->sum('amount_gs'),
            'comparaciones' => $comparaciones,
        ]);
    }
}