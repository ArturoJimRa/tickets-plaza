<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{

public function index(Request $request)
{
    $rol       = session('rol');
    $rolId     = session('rol_id');
    $usuarioId = session('usuario_id');
    $esJefe    = session('es_jefe');

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
            'tickets.fecha_limite',
            'tickets.fecha_cierre',
            'unidades.nombre as unidad',
            'roles.nombre as area',
            'categorias_ticket.nombre as categoria',
            'subcategorias_ticket.nombre as subcategoria',
            DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
            'asignado.nombre as asignado_a',
            'tickets.fecha_creacion'
        );

    if ($rol === 'Admin') {
        // ve todo
    }
    elseif ($rol === 'Unidad') {
        $query->where('tickets.usuario_id', $usuarioId);
    }
    elseif ($esJefe) {
        $query->where('tickets.rol_destino_id', $rolId);
    }
    else {
        $query->where('tickets.rol_destino_id', $rolId)
              ->where(function($q) use ($usuarioId){
                    $q->whereNull('tickets.asignado_a')
                      ->orWhere('tickets.asignado_a', $usuarioId) // 🔥 FIX
                      ->orWhere('tickets.estado_ticket_id', 4);
              });
    }

    if ($request->filled('buscar')) {
        $buscar = $request->buscar;

        $query->where(function($q) use ($buscar) {
            $q->where('tickets.titulo', 'like', "%{$buscar}%")
              ->orWhere('tickets.id', $buscar)
              ->orWhere('unidades.nombre', 'like', "%{$buscar}%")
              ->orWhere('categorias_ticket.nombre', 'like', "%{$buscar}%")
              ->orWhere('tickets.prioridad', 'like', "%{$buscar}%");
        });
    }

    if ($request->filled('fecha_inicio')) {
        $query->whereDate('tickets.fecha_creacion', '>=', $request->fecha_inicio);
    }

    if ($request->filled('fecha_fin')) {
        $query->whereDate('tickets.fecha_creacion', '<=', $request->fecha_fin);
    }

    if ($request->filled('estado')) {
        $query->where('tickets.estado_ticket_id', $request->estado);
    }

    if ($request->filled('prioridad')) {
        $query->where('tickets.prioridad', $request->prioridad);
    }

    if ($request->filled('area_id')) {
        $query->where('tickets.rol_destino_id', $request->area_id);
    }

    $tickets = $query
        ->orderBy('tickets.fecha_creacion', 'desc')
        ->get();

    $areas = DB::table('roles')
        ->whereNotIn('nombre', ['Admin', 'Unidad'])
        ->select('id', 'nombre')
        ->get();

    return view('tickets.index', compact('tickets', 'areas'));
}

public function misTickets()
{
    if (session('rol') === 'Unidad') abort(403);

    $usuarioId = session('usuario_id');

    $tickets = DB::table('tickets')
        ->leftJoin('unidades', 'tickets.unidad_id', '=', 'unidades.id')
        ->leftJoin('categorias_ticket', 'tickets.categoria_id', '=', 'categorias_ticket.id')
        ->leftJoin('subcategorias_ticket', 'tickets.subcategoria_id', '=', 'subcategorias_ticket.id')
        ->leftJoin('estados_ticket', 'tickets.estado_ticket_id', '=', 'estados_ticket.id')
        ->leftJoin('roles', 'tickets.rol_destino_id', '=', 'roles.id')

        ->where('tickets.asignado_a', $usuarioId)
        ->where(function($q){
            $q->whereNull('estados_ticket.nombre')
              ->orWhere('estados_ticket.nombre', '!=', 'Cerrado');
        })

        ->select(
            'tickets.id',
            'tickets.titulo',
            'tickets.prioridad',
            'tickets.fecha_limite',
            'tickets.fecha_cierre',
            'unidades.nombre as unidad',
            'roles.nombre as area',
            'categorias_ticket.nombre as categoria',
            DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
            'tickets.fecha_creacion'
        )
        ->orderBy('tickets.fecha_creacion', 'desc')
        ->get();

    return view('tickets.mis_tickets', compact('tickets'));
}

public function show($id)
{
    $ticket = DB::table('tickets')
        ->join('usuarios', 'tickets.usuario_id', '=', 'usuarios.id')
        ->leftJoin('unidades', 'tickets.unidad_id', '=', 'unidades.id')
        ->join('categorias_ticket', 'tickets.categoria_id', '=', 'categorias_ticket.id')
        ->leftJoin('subcategorias_ticket', 'tickets.subcategoria_id', '=', 'subcategorias_ticket.id')
        ->leftJoin('estados_ticket', 'tickets.estado_ticket_id', '=', 'estados_ticket.id')
        ->leftJoin('usuarios as asignado', 'tickets.asignado_a', '=', 'asignado.id')
        ->leftJoin('usuarios as cerrado', 'tickets.cerrado_por', '=', 'cerrado.id')
        ->leftJoin('roles', 'tickets.rol_destino_id', '=', 'roles.id')

        ->select(
            'tickets.*',
            'usuarios.nombre as creador',
            'unidades.nombre as unidad',
            'roles.nombre as area',
            'categorias_ticket.nombre as categoria',
            'subcategorias_ticket.nombre as subcategoria',
            DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
            'asignado.nombre as asignado_a',
            'asignado.id as asignado_id',
            'cerrado.nombre as cerrado_por_nombre'
        )
        ->where('tickets.id', $id)
        ->first();

    if (!$ticket) abort(404);

    $usuariosSistemas = DB::table('usuarios')
        ->where('rol_id', $ticket->rol_destino_id)
        ->where('estado', 'activo')
        ->select('id', 'nombre')
        ->get();

    $respuestas = DB::table('respuestas_ticket')
        ->join('usuarios', 'respuestas_ticket.usuario_id', '=', 'usuarios.id')
        ->where('respuestas_ticket.ticket_id', $id)
        ->orderBy('respuestas_ticket.fecha', 'asc')
        ->select('usuarios.nombre','respuestas_ticket.mensaje','respuestas_ticket.fecha')
        ->get();

    $estados = DB::table('estados_ticket')->get();

    return view('tickets.show', compact('ticket','usuariosSistemas','respuestas','estados'));
}

public function create()
{
    $roles = DB::table('roles')->get();
    return view('tickets.create', compact('roles'));
}

public function getCategoriasPorArea($rol_id)
{
    return DB::table('categorias_ticket')
        ->where('rol_destino_id', $rol_id)
        ->select('id', 'nombre')
        ->get();
}

public function getSubcategorias($categoria_id)
{
    return DB::table('subcategorias_ticket')
        ->where('categoria_id', $categoria_id)
        ->select('id', 'nombre')
        ->get();
}

public function store(Request $request)
{
    $request->validate([
        'rol_destino_id' => 'required|exists:roles,id',
        'categoria_id'   => 'required|exists:categorias_ticket,id',
        'titulo'         => 'required|string|max:255',
        'descripcion'    => 'required|string',
    ]);

    $usuario = DB::table('usuarios')->where('id', session('usuario_id'))->first();

    DB::table('tickets')->insert([
        'titulo'           => $request->titulo,
        'descripcion'      => $request->descripcion,
        'categoria_id'     => $request->categoria_id,
        'subcategoria_id'  => $request->subcategoria_id,
        'rol_destino_id'   => $request->rol_destino_id,
        'rol_origen_id'    => session('rol_id'), // 🔥 NUEVO
        'estado_ticket_id' => 1,
        'unidad_id'        => $usuario->unidad_id ?? null,
        'usuario_id'       => session('usuario_id'),
        'fecha_creacion'   => now(),
        'asignado_a'       => null,
    ]);

    return redirect('/tickets')->with('success', 'Ticket creado correctamente');
}

public function asignar(Request $request, $id)
{
    $ticket = DB::table('tickets')->where('id', $id)->first();
    if (!$ticket) abort(404);

    $request->validate([
        'asignado_a' => 'required|exists:usuarios,id'
    ]);

    DB::table('tickets')->where('id', $id)->update([
        'asignado_a' => $request->asignado_a
    ]);

    // 🔥 REDIRIGE AL LISTADO
    return redirect('/tickets')->with('success', 'Asignado correctamente');
}

public function responder(Request $request, $id)
{
    $ticket = DB::table('tickets')->where('id', $id)->first();
    if (!$ticket) abort(404);

    DB::table('respuestas_ticket')->insert([
        'ticket_id'  => $id,
        'usuario_id' => session('usuario_id'),
        'mensaje'    => $request->mensaje,
        'fecha'      => now(),
        'estado'     => 'activo'
    ]);

    $update = [
        'estado_ticket_id' => $request->estado_ticket_id
    ];

    if ($request->filled('asignado_a')) {
        $update['asignado_a'] = $request->asignado_a;

        // 🔥 SI REASIGNA → REDIRIGE
        DB::table('tickets')->where('id', $id)->update($update);
        return redirect('/tickets')->with('success', 'Ticket reasignado');
    }

    DB::table('tickets')->where('id', $id)->update($update);

    return back()->with('success', 'Respuesta guardada');
}

public function ticketsEntreAreas()
{
    $usuarioRol = session('rol_id');

    $tickets = DB::table('tickets as t')
        ->join('roles as r1', 't.rol_origen_id', '=', 'r1.id')
        ->join('roles as r2', 't.rol_destino_id', '=', 'r2.id')
        ->leftJoin('estados_ticket as e', 't.estado_ticket_id', '=', 'e.id')
        ->select(
            't.*',
            'r1.nombre as area_origen',
            'r2.nombre as area_destino',
            DB::raw("COALESCE(e.nombre, 'Abierto') as estado")
        )
        ->where('t.rol_origen_id', $usuarioRol) // 🔥 CAMBIO CLAVE
        ->whereColumn('t.rol_origen_id', '!=', 't.rol_destino_id')
        ->get();

    return view('tickets.entre_areas', compact('tickets'));
}

public function cerrar($id)
{
    abort(404); // 🔥 ELIMINADO COMO PEDISTE
}

public function exportar(Request $request)
{
    if (session('rol') !== 'Admin') abort(403);

    return Excel::download(
        new TicketsExport(
            $request->fecha_inicio,
            $request->fecha_fin,
            $request->area_id
        ),
        'reporte_tickets.xlsx'
    );
}

}