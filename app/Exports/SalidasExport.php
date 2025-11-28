<?php

namespace App\Exports;

use App\Models\salida;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SalidasExport implements FromCollection, WithHeadings
{
    protected $inicio;
    protected $fin;

    public function __construct($inicio, $fin)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
    }

    public function collection(): Collection
    {
        $query = Salida::with('user', 'tipoalerta', 'gestion');

        if ($this->inicio) $query->whereDate('fecha', '>=', $this->inicio);
        if ($this->fin) $query->whereDate('fecha', '<=', $this->fin);

        return $query->get()->map(function ($salida) {
            return [
                'ID'          => $salida->id,
                'Descripción' => $salida->descripcion,
                'Fecha'       => $salida->fecha,
                'Hora'        => $salida->hora,
                'Usuario'     => $salida->user->name ?? '',
                'Gestión'     => $salida->gestion->nombre ?? '',
                'Tipo Alerta' => $salida->tipoalerta->descripcion ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Descripción', 'Fecha', 'Hora', 'Usuario', 'Gestión', 'Tipo Alerta'
        ];
    }
}
