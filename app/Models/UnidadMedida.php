<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'unidad_medidas';

    protected $fillable = ['nombre'];

    public function producciones()
    {
        return $this->hasMany(Produccion::class, 'id_unidadmedida');
    }

    public function ofertaDetalles()
    {
        return $this->hasMany(OfertaDetalle::class, 'id_unidadmedida'); // Corrige 'ofertaDetalle' a 'OfertaDetalle'
    }

    public function pedidoDetalles()
    {
        return $this->hasMany(PedidoDetalle::class, 'id_unidadmedida');
    }
}
