<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfertaDetalle extends Model
{
    use HasFactory;

    protected $fillable = ['id_produccion', 'id_oferta', 'id_unidadmedida', 'id_moneda', 'descripcion', 'cantidad_fisico', 'cantidad_comprometido', 'precio', 'estado'];

    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'id_oferta');
    }

    public function produccion()
    {
        return $this->belongsTo(Produccion::class, 'id_produccion');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidadmedida');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }

    public function cargas()
    {
        return $this->hasMany(CargaOferta::class, 'id_oferta_detalle');
    }
}
