<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaTicket extends Model
{
    protected $table = 'categorias_ticket';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'rol_destino_id'];

    public function subcategorias()
    {
        return $this->hasMany(SubcategoriaTicket::class, 'categoria_id');
    }
}