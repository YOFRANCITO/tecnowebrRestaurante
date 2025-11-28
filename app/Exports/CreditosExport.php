<?php

namespace App\Exports;

use App\Models\Credito;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CreditosExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return Credito::query()
            ->with(['user', 'plan', 'venta'])
            ->where('saldo_final', '>', 0)
            ->orderBy('fecha', 'desc');
    }

    public function headings(): array
    {
        return [
            'Número',
            'Cliente',
            'Fecha',
            'Plan',
            'Saldo Inicial (Bs.)',
            'Interés (Bs.)',
            'Capital (Bs.)',
            'Saldo Pendiente (Bs.)',
        ];
    }

    public function map($credito): array
    {
        return [
            $credito->nro,
            $credito->user->name ?? 'N/A',
            $credito->fecha->format('d/m/Y'),
            $credito->plan->nombre ?? 'N/A',
            number_format($credito->saldo_inicial, 2, '.', ''),
            number_format($credito->interes, 2, '.', ''),
            number_format($credito->capital, 2, '.', ''),
            number_format($credito->saldo_final, 2, '.', ''),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
