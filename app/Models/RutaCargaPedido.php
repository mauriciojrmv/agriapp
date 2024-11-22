<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaCargaPedido extends Model
{
    use HasFactory;

    protected $fillable = ['id_carga_pedido', 'id_ruta_pedido', 'id_transporte', 'orden', 'estado', 'distancia'];

    public function cargaPedido()
    {
        return $this->belongsTo(CargaPedido::class, 'id_carga_pedido');
    }

    public function rutaPedido()
    {
        return $this->belongsTo(RutaPedido::class, 'id_ruta_pedido');
    }

    public function transporte()
    {
        return $this->belongsTo(Transporte::class, 'id_transporte');
    }

    public function conductor()
    {
        return $this->hasOneThrough(Conductor::class, Transporte::class, 'id_conductor', 'id', 'id_transporte', 'id');
    }
}
