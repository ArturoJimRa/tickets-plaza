<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'estado'
    ];

    public function unidades()
    {
        return $this->hasMany(Unidad::class);
    }
    public $timestamps = false;
}