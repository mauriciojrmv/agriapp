<?php

namespace Database\Seeders;

use App\Models\Transporte;
use Illuminate\Database\Seeder;

class TransportesSeeder extends Seeder
{
    public function run()
    {
        Transporte::insert([
            ['id_conductor' => 1, 'capacidadmaxkg' => 2000, 'marca' => 'Toyota', 'modelo' => 'Hilux', 'placa' => 'ABC123', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 2, 'capacidadmaxkg' => 1500, 'marca' => 'Nissan', 'modelo' => 'Frontier', 'placa' => 'DEF456', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 3, 'capacidadmaxkg' => 2500, 'marca' => 'Ford', 'modelo' => 'Ranger', 'placa' => 'GHI789', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 4, 'capacidadmaxkg' => 1800, 'marca' => 'Chevrolet', 'modelo' => 'Colorado', 'placa' => 'JKL012', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 5, 'capacidadmaxkg' => 1600, 'marca' => 'Mitsubishi', 'modelo' => 'L200', 'placa' => 'MNO345', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 6, 'capacidadmaxkg' => 2200, 'marca' => 'Isuzu', 'modelo' => 'D-Max', 'placa' => 'PQR678', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 7, 'capacidadmaxkg' => 1700, 'marca' => 'Mazda', 'modelo' => 'BT-50', 'placa' => 'STU901', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 8, 'capacidadmaxkg' => 2000, 'marca' => 'Volkswagen', 'modelo' => 'Amarok', 'placa' => 'VWX234', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 9, 'capacidadmaxkg' => 2400, 'marca' => 'Mercedes-Benz', 'modelo' => 'X-Class', 'placa' => 'YZA567', 'created_at' => now(), 'updated_at' => now()],
            ['id_conductor' => 10, 'capacidadmaxkg' => 2100, 'marca' => 'Renault', 'modelo' => 'Alaskan', 'placa' => 'BCD890', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
