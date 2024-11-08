<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['id_categoria', 'nombre', 'descripcion'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function producciones()
    {
        return $this->hasMany(Produccion::class, 'id_producto');
    }

    public function pedidoDetalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'id_producto');
    }
}
