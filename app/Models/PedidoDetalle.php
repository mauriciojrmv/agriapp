<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    use HasFactory;

    protected $fillable = ['id_pedido', 'id_producto', 'id_unidadmedida', 'cantidad', 'cantidad_ofertada', 'estado_ofertado'];

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

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidadmedida');
    }


     // Actualizar el estado de ofertado si la cantidad se cumple
     public function actualizarEstadoOfertado()
     {
         if ($this->cantidad == $this->cantidad_ofertada) {
             $this->estado_ofertado = 'ofertado';
         } else {
             $this->estado_ofertado = 'pendiente';
         }
         $this->save();
     }

}
