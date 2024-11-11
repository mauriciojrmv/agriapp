<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaPedido extends Model
{
    use HasFactory;

    protected $fillable = ['id_pedido_detalle', 'cantidad', 'estado'];

    public function pedidoDetalle()
    {
        return $this->belongsTo(PedidoDetalle::class, 'id_pedido_detalle');
    }

    public function rutas()
    {
        return $this->hasMany(RutaCargaPedido::class, 'id_carga_pedido');
    }

    public function rutaCargaPedido()
    {
        return $this->hasMany(RutaCargaPedido::class, 'id_carga_pedido', 'id');
    }
}
