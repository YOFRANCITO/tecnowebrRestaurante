<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentasExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Venta::query()->with(['user', 'mesa']);

        if (!empty($this->filters['fecha_desde'])) {
            $query->where('fecha_hora', '>=', $this->filters['fecha_desde']);
        }

        if (!empty($this->filters['fecha_hasta'])) {
            $query->where('fecha_hora', '<=', $this->filters['fecha_hasta'] . ' 23:59:59');
        }

        if (!empty($this->filters['tipo_pago'])) {
            $query->where('tipo_pago', $this->filters['tipo_pago']);
        }

        if (!empty($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }

        return $query->orderBy('fecha_hora', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha y Hora',
            'Cliente',
            'Mesa',
            'Total (Bs.)',
            'Tipo de Pago',
            'Estado',
        ];
    }

    public function map($venta): array
    {
        return [
            $venta->id,
            $venta->fecha_hora->format('d/m/Y H:i'),
            $venta->user->name ?? 'N/A',
            $venta->mesa->codigo ?? 'N/A',
            number_format($venta->total, 2, '.', ''),
            $venta->tipo_pago,
            $venta->estado,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
