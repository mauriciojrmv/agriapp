<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
    use HasFactory;

    protected $fillable = ['id_terreno', 'id_temporada', 'id_producto', 'id_unidadmedida', 'descripcion', 'cantidad', 'fecha_cosecha', 'fecha_expiracion', 'estado'];

    public function terreno()
    {
        return $this->belongsTo(Terreno::class, 'id_terreno');
    }

    public function temporada()
    {
        return $this->belongsTo(Temporada::class, 'id_temporada');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidadmedida');
    }

    public function ofertas()
    {
        return $this->hasMany(Oferta::class, 'id_produccion');
    }
}
