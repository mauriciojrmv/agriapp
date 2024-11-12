<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfertaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_produccion',
        'id_oferta',
        'id_unidadmedida',
        'id_moneda',
        'descripcion',
        'cantidad_fisico',
        'cantidad_comprometido',
        'precio',
        'preciounitario', // Agregar preciounitario aquí
        'estado'
    ];

    protected static function booted()
    {
        static::saving(function ($detalle) {
            if ($detalle->cantidad_fisico > 0) {
                $detalle->preciounitario = $detalle->precio / $detalle->cantidad_fisico;
            } else {
                $detalle->preciounitario = 0; // Evitar división por cero
            }
        });
    }

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
