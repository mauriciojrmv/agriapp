<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = ['id_cliente', 'fecha_entrega', 'ubicacion_longitud', 'ubicacion_latitud', 'estado'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'id_pedido');
    }
}
