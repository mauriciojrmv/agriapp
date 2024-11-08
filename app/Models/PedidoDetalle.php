<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    use HasFactory;

    protected $fillable = ['id_pedido', 'id_producto', 'cantidad', 'cantidad_ofertada'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function cargas()
    {
        return $this->hasMany(CargaPedido::class, 'id_pedido_detalle');
    }
}
