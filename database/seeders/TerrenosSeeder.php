<?php

namespace Database\Seeders;

use App\Models\Terreno;
use Illuminate\Database\Seeder;

class TerrenosSeeder extends Seeder
{
    public function run()
    {
        Terreno::insert([
            ['id_agricultor' => 1, 'descripcion' => 'Terreno en Plan 3000', 'area' => 120, 'superficie_total' => 120, 'ubicacion_latitud' => -17.845850, 'ubicacion_longitud' => -63.200524, 'created_at' => now(), 'updated_at' => now()],

            ['id_agricultor' => 2, 'descripcion' => 'Terreno en Villa Primero de Mayo', 'area' => 180, 'superficie_total' => 180, 'ubicacion_latitud' =>  -17.62928098981144, 'ubicacion_longitud' => -63.160523858636516, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 3, 'descripcion' => 'Terreno en Pampa de la Isla', 'area' => 200, 'superficie_total' => 200, 'ubicacion_latitud' => -17.751543, 'ubicacion_longitud' => -63.116913, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 4, 'descripcion' => 'Terreno en Los Lotes', 'area' => 150, 'superficie_total' => 150, 'ubicacion_latitud' => -17.837489, 'ubicacion_longitud' => -63.241743, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 5, 'descripcion' => 'Terreno en Parque Industrial', 'area' => 170, 'superficie_total' => 170, 'ubicacion_latitud' => -17.811111, 'ubicacion_longitud' => -63.176571, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 6, 'descripcion' => 'Terreno en El Trompillo', 'area' => 140, 'superficie_total' => 140, 'ubicacion_latitud' => -17.790432, 'ubicacion_longitud' => -63.184478, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 7, 'descripcion' => 'Terreno en Radial 17 y 1/2', 'area' => 210, 'superficie_total' => 210, 'ubicacion_latitud' => -17.782919, 'ubicacion_longitud' => -63.221153, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 8, 'descripcion' => 'Terreno en Satélite Norte', 'area' => 250, 'superficie_total' => 250, 'ubicacion_latitud' => -17.674869, 'ubicacion_longitud' => -63.155648, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 9, 'descripcion' => 'Terreno en Barrio Hamacas', 'area' => 190, 'superficie_total' => 190, 'ubicacion_latitud' => -17.786454, 'ubicacion_longitud' => -63.197498, 'created_at' => now(), 'updated_at' => now()],
            ['id_agricultor' => 10, 'descripcion' => 'Terreno en Urubó', 'area' => 230, 'superficie_total' => 230, 'ubicacion_latitud' => -17.785123, 'ubicacion_longitud' => -63.250456, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
