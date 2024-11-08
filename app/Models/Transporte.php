<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    use HasFactory;

    protected $fillable = ['id_conductor', 'capacidadmaxkg', 'marca', 'modelo', 'placa'];

    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'id_conductor');
    }
}
