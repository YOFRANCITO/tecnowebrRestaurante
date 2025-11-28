<?php

namespace App\Exports;

use App\Models\entrada;
use App\Models\Gestion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntradasExport implements FromCollection, WithHeadings
{
    protected $inicio;
    protected $fin;

    public function __construct($inicio, $fin)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
    }

    public function collection()
    {
        $query = entrada::with('user', 'tipoalerta');

        if ($this->inicio) {
            $query->whereDate('fecha', '>=', $this->inicio);
        }

        if ($this->fin) {
            $query->whereDate('fecha', '<=', $this->fin);
        }

        return $query->get()->map(function ($e) {
            return [
                'Id' => $e->id,
                'Descripcion' => $e->descripcion,
                'Fecha' => $e->fecha,
                'Hora' => $e->hora,
                'Usuario' => $e->user->name ?? '—',
                'Gestion' => $e->gestion->descripcion ?? '—',
                'Tipo_alerta' => $e->tipoalerta->descripcion ?? '—',
            ];
        });
    }

    public function headings(): array
    {
        return ['Id', 'Descripción', 'Fecha', 'Hora', 'Usuario', 'Gestión', 'Tipo de Alerta'];
    }
}
