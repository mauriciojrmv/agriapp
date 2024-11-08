<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agricultor extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellido', 'telefono', 'email', 'direccion', 'password', 'informacion_bancaria', 'nit', 'carnet', 'licencia_funcionamiento', 'estado'];

    public function terrenos()
    {
        return $this->hasMany(Terreno::class, 'id_agricultor');
    }
}
