<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('tickets')
            ->leftJoin('unidades', 'tickets.unidad_id', '=', 'unidades.id')
            ->join('categorias_ticket', 'tickets.categoria_id', '=', 'categorias_ticket.id')
            ->leftJoin('subcategorias_ticket', 'tickets.subcategoria_id', '=', 'subcategorias_ticket.id')
            ->leftJoin('estados_ticket', 'tickets.estado_ticket_id', '=', 'estados_ticket.id')
            ->leftJoin('usuarios as asignado', 'tickets.asignado_a', '=', 'asignado.id')
            ->select(
                'tickets.id',
                'tickets.titulo',
                'tickets.prioridad',
                'unidades.nombre as unidad',
                'categorias_ticket.nombre as categoria',
                'subcategorias_ticket.nombre as subcategoria',
                DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
                'asignado.nombre as asignado',
                'tickets.fecha_creacion',
                'tickets.fecha_limite',
                'tickets.fecha_cierre'
            )
            ->orderBy('tickets.fecha_creacion', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Prioridad',
            'Unidad',
            'Categoría',
            'Subcategoría',
            'Estado',
            'Asignado a',
            'Fecha creación',
            'Fecha límite',
            'Fecha cierre'
        ];
    }
}