<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize
{
    public function __construct(
        protected $household,
        protected int $month,
        protected int $year,
    ) {}

    public function collection()
    {
        return Transaction::whereHas('category', function ($q) {
                $q->where('household_id', $this->household->id);
            })
            ->with('category')
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->orderBy('date')
            ->get();
    }

    public function headings(): array
    {
        return ['Fecha', 'Tipo', 'Categoría', 'Descripción', 'Monto (Gs)'];
    }

    public function map($transaction): array
    {
        return [
            $transaction->date->format('d/m/Y'),
            $transaction->type === 'gasto' ? 'Gasto' : 'Ganancia',
            $transaction->category->name,
            $transaction->description ?? '',
            $transaction->amount_gs,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '#,##0 "Gs"',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}