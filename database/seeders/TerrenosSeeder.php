<?php

namespace Database\Seeders;

use App\Models\Terreno;
use Illuminate\Database\Seeder;

class TerrenosSeeder extends Seeder
{
    public function run()
    {
        Terreno::insert([
            ['id_agricultor' => 1, 'descripcion' => 'Terreno en zona norte', 'area' => 100, 'superficie_total' => 100, 'ubicacion_latitud' => -17.123456, 'ubicacion_longitud' => -63.123456, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 2, 'descripcion' => 'Terreno en zona sur', 'area' => 150, 'superficie_total' => 150, 'ubicacion_latitud' => -17.654321, 'ubicacion_longitud' => -63.654321, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 3, 'descripcion' => 'Terreno en zona oeste', 'area' => 200, 'superficie_total' => 200, 'ubicacion_latitud' => -17.987654, 'ubicacion_longitud' => -63.987654, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
