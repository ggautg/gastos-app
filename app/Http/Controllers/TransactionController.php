<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $transactions = auth()->user()->transactions()
            ->with('category')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $totalGastos = $transactions->where('type', 'gasto')->sum('amount');
        $totalGanancias = $transactions->where('type', 'ganancia')->sum('amount');

        $gastosPorCategoria = $transactions
            ->where('type', 'gasto')
            ->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->category->name,
                    'color' => $items->first()->category->color,
                    'total' => $items->sum('amount'),
                ];
            })
            ->values();

        $categoriasGasto = auth()->user()->categories()->where('type', 'gasto')->get();
        $categoriasConPresupuesto = $categoriasGasto->filter(fn ($cat) => $cat->budget !== null);

        $presupuestos = $categoriasConPresupuesto->map(function ($cat) use ($transactions) {
            $gastado = $transactions
                ->where('type', 'gasto')
                ->where('category_id', $cat->id)
                ->sum('amount');

            return [
                'name' => $cat->name,
                'color' => $cat->color,
                'budget' => $cat->budget,
                'gastado' => $gastado,
                'porcentaje' => $cat->budget > 0 ? round(($gastado / $cat->budget) * 100) : 0,
            ];
        })->values();

        // Armamos una fecha "ancla" con el mes/año que se está viendo,
        // y le restamos 1 mes. Carbon se encarga de pasar de año automáticamente.
        $fechaAnterior = Carbon::createFromDate($year, $month, 1)->subMonth();

        $totalesMesAnterior = auth()->user()->transactions()
            ->whereMonth('date', $fechaAnterior->month)
            ->whereYear('date', $fechaAnterior->year)
            ->get()
            ->groupBy('type')
            ->map(fn ($items) => $items->sum('amount'));

        $gastosMesAnterior = $totalesMesAnterior->get('gasto', 0);
        $gananciasMesAnterior = $totalesMesAnterior->get('ganancia', 0);

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories' => auth()->user()->categories()->orderBy('name')->get(),
            'filters' => ['month' => $month, 'year' => $year],
            'summary' => [
                'gastos' => $totalGastos,
                'ganancias' => $totalGanancias,
                'balance' => $totalGanancias - $totalGastos,
            ],
            'gastosPorCategoria' => $gastosPorCategoria,
            'presupuestos' => $presupuestos,
            'comparativa' => [
                'gastos_anterior' => $gastosMesAnterior,
                'ganancias_anterior' => $gananciasMesAnterior,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:gasto,ganancia',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        auth()->user()->transactions()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:gasto,ganancia',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $transaction->update($validated);

        return redirect()->back();
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()->back();
    }
}
