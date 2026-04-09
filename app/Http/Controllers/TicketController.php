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

    /*
    🔒 FILTRO POR ROL (TU LÓGICA ORIGINAL)
    */
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
              ->where(function($q){
                    $q->whereNull('tickets.asignado_a')
                      ->orWhere('tickets.estado_ticket_id', 4);
              });
    }

    /*
    🔎 BUSCADOR
    */
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

    /*
    📅 FILTRO POR FECHAS
    */
    if ($request->filled('fecha_inicio')) {
        $query->whereDate('tickets.fecha_creacion', '>=', $request->fecha_inicio);
    }

    if ($request->filled('fecha_fin')) {
        $query->whereDate('tickets.fecha_creacion', '<=', $request->fecha_fin);
    }

    /*
    📌 FILTRO ESTADO
    */
    if ($request->filled('estado')) {
        $query->where('tickets.estado_ticket_id', $request->estado);
    }

    /*
    ⚡ FILTRO PRIORIDAD
    */
    if ($request->filled('prioridad')) {
        $query->where('tickets.prioridad', $request->prioridad);
    }

    /*
    🏢 FILTRO ÁREA (🔥 ESTE ERA EL QUE TE FALTABA BIEN IMPLEMENTADO)
    */
    if ($request->filled('area_id')) {
        $query->where('tickets.rol_destino_id', $request->area_id);
    }

    $tickets = $query
        ->orderBy('tickets.fecha_creacion', 'desc')
        ->get();

    /*
    🔥 NECESARIO PARA EL SELECT DE ÁREAS EN LA VISTA
    */
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

    if (!$ticket) {
        abort(404);
    }

    $usuariosSistemas = DB::table('usuarios')
        ->where('rol_id', $ticket->rol_destino_id)
        ->where('estado', 'activo')
        ->select('id', 'nombre')
        ->get();

    $respuestas = DB::table('respuestas_ticket')
        ->join('usuarios', 'respuestas_ticket.usuario_id', '=', 'usuarios.id')
        ->where('respuestas_ticket.ticket_id', $id)
        ->orderBy('respuestas_ticket.fecha', 'asc')
        ->select(
            'usuarios.nombre',
            'respuestas_ticket.mensaje',
            'respuestas_ticket.fecha'
        )
        ->get();

    $estados = DB::table('estados_ticket')->get();

    return view('tickets.show', compact(
        'ticket',
        'usuariosSistemas',
        'respuestas',
        'estados'
    ));
}

/* =========================================
   🔥 AQUÍ ESTÁ LO IMPORTANTE
========================================= */

public function create()
{
    $roles = DB::table('roles')->get();

    return view('tickets.create', compact('roles'));
}

/* 🔥 NUEVO: CATEGORÍAS POR ÁREA */
public function getCategoriasPorArea($rol_id)
{
    $categorias = DB::table('categorias_ticket')
        ->where('rol_destino_id', $rol_id)
        ->select('id', 'nombre')
        ->get();

    return response()->json($categorias);
}

/* 🔥 NUEVO: SUBCATEGORÍAS */
public function getSubcategorias($categoria_id)
{
    $subcategorias = DB::table('subcategorias_ticket')
        ->where('categoria_id', $categoria_id)
        ->select('id', 'nombre')
        ->get();

    return response()->json($subcategorias);
}

public function store(Request $request)
{
    $request->validate([
        'rol_destino_id' => 'required|exists:roles,id',
        'categoria_id'   => 'required|exists:categorias_ticket,id',
        'titulo'         => 'required|string|max:255',
        'descripcion'    => 'required|string',
    ]);

    $categoria = DB::table('categorias_ticket')
        ->where('id', $request->categoria_id)
        ->where('rol_destino_id', $request->rol_destino_id)
        ->first();

    if (!$categoria) {
        return back()->withErrors(['categoria_id' => 'La categoría no pertenece al área seleccionada']);
    }

    $usuario = DB::table('usuarios')
        ->where('id', session('usuario_id'))
        ->first();

    DB::table('tickets')->insert([
        'titulo'           => $request->titulo,
        'descripcion'      => $request->descripcion,
        'categoria_id'     => $categoria->id,
        'subcategoria_id'  => $request->subcategoria_id,
        'rol_destino_id'   => $request->rol_destino_id,
        'estado_ticket_id' => 1,
        'unidad_id'        => $usuario->unidad_id ?? null,
        'usuario_id'       => session('usuario_id'),
        'fecha_creacion'   => now(),
        'asignado_a'       => null,
    ]);

    return redirect('/tickets')->with('success', 'Ticket creado correctamente');
}

/* TODO LO DEMÁS NO SE TOCA */
public function asignar(Request $request, $id)
{
    $ticket = DB::table('tickets')->where('id', $id)->first();
    if (!$ticket) abort(404);

    if (
        session('rol') !== 'Admin' &&
        $ticket->rol_destino_id != session('rol_id')
    ) {
        abort(403);
    }

    $esAdmin = session('rol') === 'Admin';

    if (!$ticket->sla_horas || $esAdmin) {
        $request->validate([
            'asignado_a' => 'required|exists:usuarios,id',
            'prioridad'  => 'required|in:critico,alto,medio,bajo'
        ]);

        $horas = 0;

        switch ($request->prioridad) {
            case 'critico': $horas = 24; break;
            case 'alto':    $horas = 72; break;
            case 'medio':   $horas = 168; break;
            case 'bajo':    $horas = 360; break;
        }

        DB::table('tickets')->where('id', $id)->update([
            'asignado_a'       => $request->asignado_a,
            'estado_ticket_id' => 2,
            'prioridad'        => $request->prioridad,
            'sla_horas'        => $horas,
            'fecha_asignacion' => now(),
            'fecha_limite'     => now()->addHours($horas)
        ]);

    } else {
        $request->validate([
            'asignado_a' => 'required|exists:usuarios,id'
        ]);

        DB::table('tickets')->where('id', $id)->update([
            'asignado_a' => $request->asignado_a
        ]);
    }

    return back()->with('success', 'Ticket actualizado correctamente');
}

public function responder(Request $request, $id)
{
    $ticket = DB::table('tickets')->where('id', $id)->first();
    if (!$ticket) abort(404);

    if (
        session('rol') !== 'Admin' &&
        $ticket->rol_destino_id != session('rol_id') &&
        $ticket->asignado_a != session('usuario_id')
    ) {
        abort(403);
    }

    $request->validate([
        'mensaje'          => 'required',
        'estado_ticket_id' => 'required|exists:estados_ticket,id'
    ]);

    DB::table('respuestas_ticket')->insert([
        'ticket_id'  => $id,
        'usuario_id' => session('usuario_id'),
        'mensaje'    => $request->mensaje,
        'fecha'      => now(),
        'estado'     => 'activo'
    ]);

    DB::table('tickets')->where('id', $id)->update([
        'estado_ticket_id' => $request->estado_ticket_id
    ]);

    return back()->with('success', 'Respuesta enviada correctamente');
}

public function cerrar($id)
{
    $ticket = DB::table('tickets')->where('id', $id)->first();

    if (!$ticket) abort(404);

    if (
        session('rol') !== 'Admin' &&
        $ticket->rol_destino_id != session('rol_id') &&
        $ticket->asignado_a != session('usuario_id')
    ) {
        abort(403);
    }

    DB::table('tickets')->where('id', $id)->update([
        'estado_ticket_id' => 4,
        'fecha_cierre'     => now(),
        'cerrado_por'      => session('usuario_id')
    ]);

    return back()->with('success', 'Ticket cerrado correctamente');
}

public function exportar(Request $request)
{
    // 🔒 SOLO ADMIN
    if (session('rol') !== 'Admin') {
        abort(403);
    }

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