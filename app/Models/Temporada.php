<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'descripcion'];

    public function producciones()
    {
        return $this->hasMany(Produccion::class, 'id_temporada');
    }
}
