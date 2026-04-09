<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class TicketsExport implements FromCollection, WithHeadings
{
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $area_id;

    public function __construct($fecha_inicio = null, $fecha_fin = null, $area_id = null)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin    = $fecha_fin;
        $this->area_id      = $area_id;
    }

    public function collection()
    {
        $query = DB::table('tickets')
            ->leftJoin('unidades', 'tickets.unidad_id', '=', 'unidades.id')
            ->join('categorias_ticket', 'tickets.categoria_id', '=', 'categorias_ticket.id')
            ->leftJoin('subcategorias_ticket', 'tickets.subcategoria_id', '=', 'subcategorias_ticket.id')
            ->leftJoin('estados_ticket', 'tickets.estado_ticket_id', '=', 'estados_ticket.id')
            ->leftJoin('usuarios as asignado', 'tickets.asignado_a', '=', 'asignado.id')
            ->leftJoin('roles', 'tickets.rol_destino_id', '=', 'roles.id')

            ->select(
                'tickets.id',
                'tickets.titulo',
                'tickets.prioridad',
                'unidades.nombre as unidad',
                'unidades.razon_social',
                'roles.nombre as area',
                'categorias_ticket.nombre as categoria',
                'subcategorias_ticket.nombre as subcategoria',
                DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
                'asignado.nombre as asignado',
                'tickets.fecha_creacion',
                'tickets.fecha_limite',
                'tickets.fecha_cierre'
            );

        /*
        🔥 FILTRO POR FECHAS (CORRECTO CON HORAS)
        */
        if ($this->fecha_inicio) {
            $inicio = Carbon::parse($this->fecha_inicio)->startOfDay(); // 00:00:00
            $query->where('tickets.fecha_creacion', '>=', $inicio);
        }

        if ($this->fecha_fin) {
            $fin = Carbon::parse($this->fecha_fin)->endOfDay(); // 23:59:59
            $query->where('tickets.fecha_creacion', '<=', $fin);
        }

        /*
        🔥 FILTRO POR ÁREA
        */
        if ($this->area_id) {
            $query->where('tickets.rol_destino_id', $this->area_id);
        }

        return $query->orderBy('tickets.fecha_creacion', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Prioridad',
            'Unidad',
            'Razón Social',
            'Área',
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