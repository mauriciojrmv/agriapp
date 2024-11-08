<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function ofertaDetalles()
    {
        return $this->hasMany(OfertaDetalle::class, 'id_moneda');
    }
}
