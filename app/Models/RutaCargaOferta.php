<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaCargaOferta extends Model
{
    use HasFactory;

    protected $fillable = ['id_carga_oferta', 'id_ruta_oferta', 'id_transporte', 'orden', 'estado', 'distancia'];

    public function cargaOferta()
    {
        return $this->belongsTo(CargaOferta::class, 'id_carga_oferta');
    }

    public function rutaOferta()
    {
        return $this->belongsTo(RutaOferta::class, 'id_ruta_oferta');
    }

    public function transporte()
    {
        return $this->belongsTo(Transporte::class, 'id_transporte');
    }
}
