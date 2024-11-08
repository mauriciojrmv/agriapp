<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function producciones()
    {
        return $this->hasMany(Produccion::class, 'id_unidadmedida');
    }
}
