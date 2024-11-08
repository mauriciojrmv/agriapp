<?php

namespace Database\Seeders;

use App\Models\Conductor;
use Illuminate\Database\Seeder;

class ConductoresSeeder extends Seeder
{
    public function run()
    {
        Conductor::insert([
            [
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'carnet' => '12345678',
                'licencia_conducir' => 'A12345',
                'fecha_nacimiento' => '1985-05-15',
                'direccion' => 'Av. Siempre Viva 123',
                'email' => 'juan.perez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.123456,
                'ubicacion_longitud' => -63.123456,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Martinez',
                'carnet' => '87654321',
                'licencia_conducir' => 'B67890',
                'fecha_nacimiento' => '1990-07-22',
                'direccion' => 'Calle Falsa 456',
                'email' => 'luis.martinez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.654321,
                'ubicacion_longitud' => -63.654321,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Garcia',
                'carnet' => '11223344',
                'licencia_conducir' => 'C12345',
                'fecha_nacimiento' => '1988-09-10',
                'direccion' => 'Calle 8 #90',
                'email' => 'ana.garcia@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.987654,
                'ubicacion_longitud' => -63.987654,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
