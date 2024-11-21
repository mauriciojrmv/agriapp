<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaOferta extends Model
{
    use HasFactory;

    protected $fillable = ['id_oferta_detalle', 'pesokg', 'precio', 'estado'];


    public function ofertaDetalle()
    {
        return $this->belongsTo(OfertaDetalle::class, 'id_oferta_detalle');
    }

    public function rutas()
    {
        return $this->hasMany(RutaCargaOferta::class, 'id_carga_oferta');
    }
}
