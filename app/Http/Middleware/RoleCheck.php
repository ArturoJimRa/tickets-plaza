<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleCheck
{
    public function handle(Request $request, Closure $next, $rol)
    {

        if (session('rol') != $rol) {
            abort(403, 'No tienes permisos');
        }

        return $next($request);
    }
}
