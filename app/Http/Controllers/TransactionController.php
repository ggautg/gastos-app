<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Carga, edición, borrado y exportación de los movimientos del hogar activo.
 */
class TransactionController extends Controller
{
    /**
     * Lista los movimientos del mes y año elegidos con resumen, presupuestos, comparativa y rol.
     */
    public function index(Request $request)
    {
        $household = $this->currentHousehold();

        $month = $this->month($request);
        $year = $this->year($request);

        $transactions = Transaction::whereHas('category', function ($q) use ($household) {
            $q->where('household_id', $household->id);
        })
            ->with('category')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            // Mismo día: el último cargado queda arriba (comportamiento de pila).
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $totalGastos = $transactions->where('type', 'gasto')->sum('amount_gs');
        $totalGanancias = $transactions->where('type', 'ganancia')->sum('amount_gs');
        $gastosPorCategoria = $transactions
            ->where('type', 'gasto')
            ->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'id' => $items->first()->category_id,
                    'name' => $items->first()->category->name,
                    'color' => $items->first()->category->color,
                    'total' => $items->sum('amount_gs'),
                ];
            })
            ->values();
        $categorias = $household->categories()->orderBy('name')->get();
        $categoriasConPresupuesto = $categorias->where('type', 'gasto')->filter(fn($cat) => $cat->budget !== null);
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
            ->map(fn($items) => $items->sum('amount_gs'));

        $gastosMesAnterior = $totalesMesAnterior->get('gasto', 0);
        $gananciasMesAnterior = $totalesMesAnterior->get('ganancia', 0);

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categorias,
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
            'role' => $this->roleIn($household),
        ]);
    }

    /**
     * Carga un movimiento en PYG; requiere dueño y que la categoría coincida con el tipo elegido.
     */
    public function store(Request $request)
    {
        $household = $this->currentHousehold();
        $this->authorizeViewerCannotEdit($household);

        $validated = $request->validate([
            'category_id' => ['required', Rule::in($household->categories()->pluck('id'))],
            'type' => 'required|in:gasto,ganancia',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $this->assertCategoriaDelTipo($household, $validated);

        $validated['currency'] = 'PYG';
        $validated['exchange_rate'] = null;
        $validated['amount_gs'] = $validated['amount'];
        $validated['user_id'] = auth()->id();

        Transaction::create($validated);

        return redirect()->back();
    }

    /**
     * Edita un movimiento del hogar; requiere dueño, categoría del hogar y tipo coherente.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $household = $this->currentHousehold();
        $this->authorizeViewerCannotEdit($household);
        abort_unless($transaction->category->household_id === $household->id, 404);

        $validated = $request->validate([
            'category_id' => ['required', Rule::in($household->categories()->pluck('id'))],
            'type' => 'required|in:gasto,ganancia',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $this->assertCategoriaDelTipo($household, $validated);

        $validated['currency'] = 'PYG';
        $validated['exchange_rate'] = null;
        $validated['amount_gs'] = $validated['amount'];

        $transaction->update($validated);

        return redirect()->back();
    }
    /**
     * Borra un movimiento del hogar; requiere permiso de dueño y que pertenezca al hogar activo.
     */
    public function destroy(Transaction $transaction)
    {
        $household = $this->currentHousehold();
        $this->authorizeViewerCannotEdit($household);
        abort_unless($transaction->category->household_id === $household->id, 404);

        $transaction->delete();

        return redirect()->back();
    }

    /**
     * Descarga en XLSX los movimientos del mes y año elegidos, con filtro de día opcional.
     */
    public function exportExcel(Request $request)
    {
        $household = $this->currentHousehold();
        $month = $this->month($request);
        $year = $this->year($request);
        $day = $this->day($request);

        $nombreArchivo = $day
            ? "movimientos_{$year}_{$month}_{$day}.xlsx"
            : "movimientos_{$year}_{$month}.xlsx";

        return Excel::download(
            new TransactionsExport($household, $month, $year, $day),
            $nombreArchivo
        );
    }

    /**
     * Genera y descarga un PDF con el resumen de gastos y ganancias del período elegido.
     */
    public function exportPdf(Request $request)
    {
        $household = $this->currentHousehold();
        $month = $this->month($request);
        $year = $this->year($request);
        $day = $this->day($request);

        $transactions = Transaction::whereHas('category', function ($q) use ($household) {
            $q->where('household_id', $household->id);
        })
            ->with('category')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->when($day, fn ($query) => $query->whereDay('date', $day))
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $nombresMeses = [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];

        $nombreMes = $nombresMeses[$month - 1];
        $titulo = $day
            ? "Resumen del {$day} de {$nombreMes} de {$year}"
            : "Resumen de {$nombreMes} {$year}";

        $pdf = Pdf::loadView('pdf.monthly-summary', [
            'household' => $household,
            'transactions' => $transactions,
            'month' => $month,
            'year' => $year,
            'nombreMes' => $nombreMes,
            'titulo' => $titulo,
            'totalGastos' => $transactions->where('type', 'gasto')->sum('amount_gs'),
            'totalGanancias' => $transactions->where('type', 'ganancia')->sum('amount_gs'),
        ]);

        $nombreArchivo = $day
            ? "resumen_{$year}_{$month}_{$day}.pdf"
            : "resumen_{$year}_{$month}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Valida que la categoría elegida sea del mismo tipo (gasto o ganancia) que el movimiento.
     */
    private function assertCategoriaDelTipo($household, array $validated): void
    {
        $categoria = $household->categories()->findOrFail($validated['category_id']);

        if ($categoria->type !== $validated['type']) {
            throw ValidationException::withMessages([
                'category_id' => 'La categoría elegida no coincide con el tipo. Elegí una categoría de Gasto o de Ganancia según corresponda.',
            ]);
        }
    }
}
