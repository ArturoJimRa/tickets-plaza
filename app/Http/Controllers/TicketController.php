<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
public function index()
{
    $rol       = session('rol');
    $rolId     = session('rol_id');
    $usuarioId = session('usuario_id');

    $query = DB::table('tickets')
        ->leftJoin('unidades', 'tickets.unidad_id', '=', 'unidades.id')
        ->join('categorias_ticket', 'tickets.categoria_id', '=', 'categorias_ticket.id')
        ->leftJoin('subcategorias_ticket', 'tickets.subcategoria_id', '=', 'subcategorias_ticket.id')
        ->leftJoin('estados_ticket', 'tickets.estado_ticket_id', '=', 'estados_ticket.id')
        ->leftJoin('usuarios as asignado', 'tickets.asignado_a', '=', 'asignado.id')
        ->select(
            'tickets.id',
            'tickets.titulo',

            // 🔥 AGREGADO (NO AFECTA NADA)
            'tickets.prioridad',
            'tickets.fecha_limite',
            'tickets.fecha_cierre',
            'unidades.nombre as unidad',
            'categorias_ticket.nombre as categoria',
            'subcategorias_ticket.nombre as subcategoria',
            DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
            'asignado.nombre as asignado_a',
            'tickets.fecha_creacion'
        );

    /*
    ===============================
    FILTROS SEGÚN ROL
    ===============================
    */

    // 👨‍💼 ADMIN → VE TODO
    if ($rol === 'Admin') {
        // No se aplica ningún filtro
    }

    // 🏢 USUARIO DE UNIDAD → SOLO SUS TICKETS
    elseif ($rol === 'Unidad') {
        $query->where('tickets.usuario_id', $usuarioId);
    }

    // 🧑‍💻 ÁREA DESTINO → SOLO NO ASIGNADOS O CERRADOS
    else {
        $query->where('tickets.rol_destino_id', $rolId)
              ->where(function($q){
                    $q->whereNull('tickets.asignado_a')
                      ->orWhere('tickets.estado_ticket_id', 4);
              });
    }

    /*
    ===============================
    🔎 FILTRO GENERAL
    ===============================
    */

    if (request()->filled('buscar')) {
        $buscar = request('buscar');

        $query->where(function($q) use ($buscar) {
            $q->where('tickets.titulo', 'like', "%{$buscar}%")
              ->orWhere('tickets.id', $buscar)
              ->orWhere('unidades.nombre', 'like', "%{$buscar}%")
              ->orWhere('categorias_ticket.nombre', 'like', "%{$buscar}%")
              ->orWhere('tickets.prioridad', 'like', "%{$buscar}%");
        });
    }

    $tickets = $query
        ->orderBy('tickets.fecha_creacion', 'desc')
        ->get();

    return view('tickets.index', compact('tickets'));
}

    /* ===============================
       MIS TICKETS (ASIGNADOS A MÍ)
    =============================== */
public function misTickets()
{
    if (session('rol') === 'Unidad') abort(403);

    $usuarioId = session('usuario_id');

    $tickets = DB::table('tickets')
        ->leftJoin('unidades', 'tickets.unidad_id', '=', 'unidades.id')
        ->leftJoin('categorias_ticket', 'tickets.categoria_id', '=', 'categorias_ticket.id')
        ->leftJoin('subcategorias_ticket', 'tickets.subcategoria_id', '=', 'subcategorias_ticket.id')
        ->leftJoin('estados_ticket', 'tickets.estado_ticket_id', '=', 'estados_ticket.id')
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
            'categorias_ticket.nombre as categoria',
            DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
            'tickets.fecha_creacion'
        )
        ->orderBy('tickets.fecha_creacion', 'desc')
        ->get();

    return view('tickets.mis_tickets', compact('tickets'));
}

    /* ===============================
       DETALLE DEL TICKET
    =============================== */
public function show($id)
{
    $ticket = DB::table('tickets')
        ->join('usuarios', 'tickets.usuario_id', '=', 'usuarios.id')
        ->leftJoin('unidades', 'tickets.unidad_id', '=', 'unidades.id') // ✅ CAMBIO
        ->join('categorias_ticket', 'tickets.categoria_id', '=', 'categorias_ticket.id')
        ->leftJoin('subcategorias_ticket', 'tickets.subcategoria_id', '=', 'subcategorias_ticket.id')
        ->leftJoin('estados_ticket', 'tickets.estado_ticket_id', '=', 'estados_ticket.id')
        ->leftJoin('usuarios as asignado', 'tickets.asignado_a', '=', 'asignado.id')
        ->leftJoin('usuarios as cerrado', 'tickets.cerrado_por', '=', 'cerrado.id') // 👈 NUEVO
        ->select(
            'tickets.*',
            'usuarios.nombre as creador',
            'unidades.nombre as unidad',
            'categorias_ticket.nombre as categoria',
            'subcategorias_ticket.nombre as subcategoria',
            DB::raw("COALESCE(estados_ticket.nombre, 'Abierto') as estado"),
            'asignado.nombre as asignado_a',
            'asignado.id as asignado_id',
            'cerrado.nombre as cerrado_por_nombre' // 👈 NUEVO
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

    /* =============================== 
    ASIGNAR TICKET (POR ÁREA) + SLA
    =============================== */
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

    if ($ticket->estado_ticket_id == 2 && $ticket->prioridad !== null) {
        return back()->with('error', 'La prioridad no se puede modificar después de asignar el ticket');
    }

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

    $fechaAsignacion = now();
    $fechaLimite = now()->addHours($horas);

    DB::table('tickets')->where('id', $id)->update([
        'asignado_a'       => $request->asignado_a,
        'estado_ticket_id' => 2,
        'prioridad'        => $request->prioridad,
        'sla_horas'        => $horas,
        'fecha_asignacion' => $fechaAsignacion,
        'fecha_limite'     => $fechaLimite
    ]);

    return back()->with('success', 'Ticket asignado con SLA automático');
}

    /* ===============================
       RESPONDER TICKET (POR ÁREA)
    =============================== */
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

    /* ===============================
       CREAR TICKET
=============================== */
public function create()
{
    $categorias = DB::table('categorias_ticket')->get();
    return view('tickets.create', compact('categorias'));
}

public function store(Request $request)
{
    $request->validate([
        'categoria_id' => 'required|exists:categorias_ticket,id',
        'titulo'       => 'required|string|max:255',
        'descripcion'  => 'required|string',
    ]);

    $categoria = DB::table('categorias_ticket')
        ->where('id', $request->categoria_id)
        ->first();

    $usuario = DB::table('usuarios')
        ->where('id', session('usuario_id'))
        ->first();

    DB::table('tickets')->insert([
        'titulo'           => $request->titulo,
        'descripcion'      => $request->descripcion,
        'categoria_id'     => $categoria->id,
        'subcategoria_id' => $request->subcategoria_id,
        'rol_destino_id'   => $categoria->rol_destino_id,
        'estado_ticket_id' => 1,
        'unidad_id'        => $usuario->unidad_id ?? null,
        'usuario_id'       => session('usuario_id'),
        'fecha_creacion'   => now(),
        'asignado_a'       => null,
    ]);

    return redirect('/tickets')->with('success', 'Ticket creado correctamente');
}

/* ===============================
   CERRAR TICKET
=============================== */
public function cerrar($id)
{
    $ticket = DB::table('tickets')->where('id', $id)->first();

    if (!$ticket) {
        abort(404);
    }

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
}