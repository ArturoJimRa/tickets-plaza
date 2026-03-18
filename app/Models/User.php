<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'usuarios';

    public $timestamps = false; // 🔥 CLAVE

    protected $fillable = [
        'nombre',
        'correo',
        'contrasena',
        'rol_id',
        'unidad_id',
        'estado'
    ];
}
