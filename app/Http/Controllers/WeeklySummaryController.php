<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class WeeklySummaryController extends Controller
{
    public function index(Request $request)
    {
        $household = auth()->user()->currentHousehold();
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        $transacciones = Transaction::whereHas('category', function ($q) use ($household) {
                $q->where('household_id', $household->id);
            })
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('type', 'gasto')
            ->get();

        $diasEnElMes = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $bloques = collect();
        $diaInicio = 1;

        while ($diaInicio <= $diasEnElMes) {
            $diaFin = min($diaInicio + 6, $diasEnElMes);

            $totalBloque = $transacciones
                ->filter(fn ($t) => $t->date->day >= $diaInicio && $t->date->day <= $diaFin)
                ->sum('amount_gs');

            $bloques->push([
                'etiqueta' => "{$diaInicio}–{$diaFin}",
                'total' => $totalBloque,
            ]);

            $diaInicio += 7;
        }

        $bloques = $bloques->map(function ($bloque, $i) use ($bloques) {
            $anterior = $i > 0 ? $bloques[$i - 1]['total'] : null;

            $bloque['diferencia'] = $anterior !== null ? $bloque['total'] - $anterior : null;
            $bloque['porcentaje'] = ($anterior !== null && $anterior > 0)
                ? round((($bloque['total'] - $anterior) / $anterior) * 100)
                : null;

            return $bloque;
        });

        dd($bloques);
        
        return Inertia::render('Transactions/Weekly', [
            'month' => $month,
            'year' => $year,
            'bloques' => $bloques,
        ]);
    }
}