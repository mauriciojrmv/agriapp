<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaOferta extends Model
{
    use HasFactory;

    protected $fillable = ['fecha_recogida', 'capacidad_utilizada', 'distancia_total', 'estado'];

    public function rutaCargas()
    {
        return $this->hasMany(RutaCargaOferta::class, 'id_ruta_oferta');
    }
}
