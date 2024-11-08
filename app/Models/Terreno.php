<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terreno extends Model
{
    use HasFactory;

    protected $fillable = ['id_agricultor', 'descripcion', 'area', 'superficie_total', 'ubicacion_latitud', 'ubicacion_longitud'];

    public function agricultor()
    {
        return $this->belongsTo(Agricultor::class, 'id_agricultor');
    }

    public function producciones()
    {
        return $this->hasMany(Produccion::class, 'id_terreno');
    }
}
