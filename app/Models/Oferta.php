<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    use HasFactory;

    protected $fillable = ['id_produccion', 'fecha_creacion', 'fecha_expiracion', 'estado'];

    public function produccion()
    {
        return $this->belongsTo(Produccion::class, 'id_produccion');
    }

    public function detalles()
    {
        return $this->hasMany(OfertaDetalle::class, 'id_oferta');
    }
}
