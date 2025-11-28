<?php

namespace App\Exports;

use App\Models\Compra;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComprasExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Compra::query()->with(['proveedor', 'user']);

        if (!empty($this->filters['fecha_desde'])) {
            $query->where('created_at', '>=', $this->filters['fecha_desde']);
        }

        if (!empty($this->filters['fecha_hasta'])) {
            $query->where('created_at', '<=', $this->filters['fecha_hasta'] . ' 23:59:59');
        }

        if (!empty($this->filters['proveedor_id'])) {
            $query->where('proveedor_id', $this->filters['proveedor_id']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Proveedor',
            'Usuario',
            'Total (Bs.)',
        ];
    }

    public function map($compra): array
    {
        return [
            $compra->id,
            $compra->created_at->format('d/m/Y H:i'),
            $compra->proveedor->nombre ?? 'N/A',
            $compra->user->name ?? 'N/A',
            number_format($compra->total, 2, '.', ''),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
