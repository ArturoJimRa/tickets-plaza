<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidades';

    public $timestamps = false; // 🔥 CLAVE

    protected $fillable = [
        'nombre',
        'razon_social',
        'marca_id',
        'estado'
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}