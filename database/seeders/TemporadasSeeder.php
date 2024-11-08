<?php

namespace Database\Seeders;

use App\Models\Temporada;
use Illuminate\Database\Seeder;

class TemporadasSeeder extends Seeder
{
    public function run()
    {
        Temporada::insert([
            [
                'nombre' => 'Primavera 2024',
                'fecha_inicio' => '2024-03-21',
                'fecha_fin' => '2024-06-21',
                'descripcion' => 'Temporada de primavera 2024',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Verano 2024',
                'fecha_inicio' => '2024-06-22',
                'fecha_fin' => '2024-09-23',
                'descripcion' => 'Temporada de verano 2024',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Otoño 2024',
                'fecha_inicio' => '2024-09-24',
                'fecha_fin' => '2024-12-21',
                'descripcion' => 'Temporada de otoño 2024',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
