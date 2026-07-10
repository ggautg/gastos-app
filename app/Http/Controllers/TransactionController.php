<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\Transaction;
use App\Services\ExchangeRateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $household = auth()->user()->currentHousehold();

        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $transactions = Transaction::whereHas('category', function ($q) use ($household) {
            $q->where('household_id', $household->id);
        })
            ->with('category')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $totalGastos = $transactions->where('type', 'gasto')->sum('amount_gs');
        $totalGanancias = $transactions->where('type', 'ganancia')->sum('amount_gs');

        $gastosPorCategoria = $transactions
            ->where('type', 'gasto')
            ->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->category->name,
                    'color' => $items->first()->category->color,
                    'total' => $items->sum('amount_gs'),
                ];
            })
            ->values();

        $categoriasGasto = $household->categories()->where('type', 'gasto')->get();
        $categoriasConPresupuesto = $categoriasGasto->filter(fn ($cat) => $cat->budget !== null);
        $presupuestos = $categoriasConPresupuesto->map(function ($cat) use ($transactions) {
            $gastado = $transactions
                ->where('type', 'gasto')
                ->where('category_id', $cat->id)
                ->sum('amount_gs');

            return [
                'name' => $cat->name,
                'color' => $cat->color,
                'budget' => $cat->budget,
                'gastado' => $gastado,
                'porcentaje' => $cat->budget > 0 ? round(($gastado / $cat->budget) * 100) : 0,
            ];
        })->values();

        $fechaAnterior = Carbon::createFromDate($year, $month, 1)->subMonth();
        $totalesMesAnterior = Transaction::whereHas('category', function ($q) use ($household) {
            $q->where('household_id', $household->id);
        })
            ->whereMonth('date', $fechaAnterior->month)
            ->whereYear('date', $fechaAnterior->year)
            ->get()
            ->groupBy('type')
            ->map(fn ($items) => $items->sum('amount_gs'));

        $gastosMesAnterior = $totalesMesAnterior->get('gasto', 0);
        $gananciasMesAnterior = $totalesMesAnterior->get('ganancia', 0);

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories' => $household->categories()->orderBy('name')->get(),
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
            'role' => auth()->user()->households()->first()->pivot->role,
        ]);
    }

    public function store(Request $request, ExchangeRateService $exchangeRateService)
    {
        $this->authorizeViewerCannotEdit();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:gasto,ganancia',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:PYG,USD',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        if ($validated['currency'] === 'USD') {
            $rate = $exchangeRateService->getUsdToGsRate();
            $validated['exchange_rate'] = $rate;
            $validated['amount_gs'] = round($validated['amount'] * $rate);
        } else {
            $validated['exchange_rate'] = null;
            $validated['amount_gs'] = $validated['amount'];
        }

        $validated['user_id'] = auth()->id();

        Transaction::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Transaction $transaction, ExchangeRateService $exchangeRateService)
    {
        $this->authorizeViewerCannotEdit();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:gasto,ganancia',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:PYG,USD',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        if ($validated['currency'] === 'USD') {
            $rate = $exchangeRateService->getUsdToGsRate();
            $validated['exchange_rate'] = $rate;
            $validated['amount_gs'] = round($validated['amount'] * $rate);
        } else {
            $validated['exchange_rate'] = null;
            $validated['amount_gs'] = $validated['amount'];
        }

        $transaction->update($validated);

        return redirect()->back();
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeViewerCannotEdit();

        $transaction->delete();

        return redirect()->back();
    }

    private function authorizeViewerCannotEdit(): void
    {
        $role = auth()->user()->households()->first()->pivot->role;

        if ($role === 'viewer') {
            abort(403, 'No tenés permiso para modificar datos en este household.');
        }
    }

    public function exportExcel(Request $request)
    {
        $household = auth()->user()->currentHousehold();
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $nombreArchivo = "movimientos_{$year}_{$month}.xlsx";

        return Excel::download(
            new TransactionsExport($household, $month, $year),
            $nombreArchivo
        );
    }

    public function exportPdf(Request $request)
    {
        $household = auth()->user()->currentHousehold();
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $transactions = Transaction::whereHas('category', function ($q) use ($household) {
            $q->where('household_id', $household->id);
        })
            ->with('category')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $nombresMeses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
        ];

        $pdf = Pdf::loadView('pdf.monthly-summary', [
            'household' => $household,
            'transactions' => $transactions,
            'month' => $month,
            'year' => $year,
            'nombreMes' => $nombresMeses[$month - 1],
            'totalGastos' => $transactions->where('type', 'gasto')->sum('amount_gs'),
            'totalGanancias' => $transactions->where('type', 'ganancia')->sum('amount_gs'),
        ]);

        return $pdf->download("resumen_{$year}_{$month}.pdf");
    }
}
