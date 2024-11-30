<?php

namespace Database\Seeders;

use App\Models\Transporte;
use Illuminate\Database\Seeder;

class TransportesSeeder extends Seeder
{
    public function run()
    {
        Transporte::insert([
            ['id_conductor' => 1, 'capacidadmaxkg' => 1500, 'marca' => 'Toyota', 'modelo' => 'Hilux', 'placa' => 'ABC123', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 2, 'capacidadmaxkg' => 1500, 'marca' => 'Nissan', 'modelo' => 'Frontier', 'placa' => 'DEF456', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 3, 'capacidadmaxkg' => 1000, 'marca' => 'Ford', 'modelo' => 'Ranger', 'placa' => 'GHI789', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 4, 'capacidadmaxkg' => 1800, 'marca' => 'Chevrolet', 'modelo' => 'Colorado', 'placa' => 'JKL012', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 5, 'capacidadmaxkg' => 1600, 'marca' => 'Mitsubishi', 'modelo' => 'L200', 'placa' => 'MNO345', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
