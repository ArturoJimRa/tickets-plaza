<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubcategoriaTicket extends Model
{
    protected $table = 'subcategorias_ticket';

    public $timestamps = false;

    protected $fillable = ['nombre', 'categoria_id'];

    public function categoria()
    {
        return $this->belongsTo(CategoriaTicket::class, 'categoria_id');
    }
}