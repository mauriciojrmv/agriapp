<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellido', 'carnet', 'licencia_conducir', 'fecha_nacimiento', 'direccion', 'email', 'password', 'ubicacion_latitud', 'ubicacion_longitud', 'estado'];

    public function transportes()
    {
        return $this->hasMany(Transporte::class, 'id_conductor');
    }
}
