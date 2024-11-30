<?php

namespace Database\Seeders;

use App\Models\Conductor;
use Illuminate\Database\Seeder;

class ConductoresSeeder extends Seeder
{
    public function run()
    {
        Conductor::insert([
            ['nombre' => 'Juan Pérez', 'ubicacion_latitud' => -17.783902, 'ubicacion_longitud' => -63.182093, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Carlos López', 'ubicacion_latitud' => -17.790234, 'ubicacion_longitud' => -63.175678, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'María Rodríguez', 'ubicacion_latitud' => -17.776543, 'ubicacion_longitud' => -63.210987, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'José Martínez', 'ubicacion_latitud' => -17.765432, 'ubicacion_longitud' => -63.199876, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ana Gómez', 'ubicacion_latitud' => -17.800123, 'ubicacion_longitud' => -63.190123, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
