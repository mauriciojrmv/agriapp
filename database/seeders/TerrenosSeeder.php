<?php

namespace Database\Seeders;

use App\Models\Terreno;
use Illuminate\Database\Seeder;

class TerrenosSeeder extends Seeder
{
    public function run()
    {
        Terreno::insert([
            ['id_agricultor' => 1, 'descripcion' => 'Terreno en Comunidad el Fortin', 'area' => 120, 'superficie_total' => 120, 'ubicacion_latitud' => -17.123456, 'ubicacion_longitud' => -63.123456, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 2, 'descripcion' => 'Terreno en Carbones', 'area' => 180, 'superficie_total' => 180, 'ubicacion_latitud' => -17.654321, 'ubicacion_longitud' => -63.654321, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 3, 'descripcion' => 'Terreno en Venadillos', 'area' => 200, 'superficie_total' => 200, 'ubicacion_latitud' => -17.987654, 'ubicacion_longitud' => -63.987654, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 4, 'descripcion' => 'Terreno en Comunidad el Fortin 2', 'area' => 150, 'superficie_total' => 150, 'ubicacion_latitud' => -17.134567, 'ubicacion_longitud' => -63.134567, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 5, 'descripcion' => 'Terreno en Las Anguilas', 'area' => 170, 'superficie_total' => 170, 'ubicacion_latitud' => -17.665432, 'ubicacion_longitud' => -62.665432, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 6, 'descripcion' => 'Terreno en Estancia el Colorado', 'area' => 140, 'superficie_total' => 140, 'ubicacion_latitud' => -17.976543, 'ubicacion_longitud' => -62.976543, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 7, 'descripcion' => 'Terreno en Comunidad el Fortin 3', 'area' => 210, 'superficie_total' => 210, 'ubicacion_latitud' => -17.145678, 'ubicacion_longitud' => -63.145678, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 8, 'descripcion' => 'Terreno en Carbones 2', 'area' => 250, 'superficie_total' => 250, 'ubicacion_latitud' => -17.676543, 'ubicacion_longitud' => -63.676543, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 9, 'descripcion' => 'Terreno en Venadillos 2', 'area' => 190, 'superficie_total' => 190, 'ubicacion_latitud' => -17.965432, 'ubicacion_longitud' => -63.965432, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 10, 'descripcion' => 'Terreno en Comunidad el Fortin 4', 'area' => 220, 'superficie_total' => 220, 'ubicacion_latitud' => -17.156789, 'ubicacion_longitud' => -63.156789, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
