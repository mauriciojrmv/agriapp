<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_entrega',
        'capacidad_utilizada',
        'distancia_total',
        'estado'
    ];

    // Relación con RutaCargaPedido
    public function rutaCargas()
    {
        return $this->hasMany(RutaCargaPedido::class, 'id_ruta_pedido', 'id');
    }
}
