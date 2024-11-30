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
                'direccion' => 'Av. Cristo Redentor 123',
                'email' => 'juan.perez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.764283,
                'ubicacion_longitud' => -63.157857,
                'estado' => 'activo',
                'tipo' => 'recogo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Martinez',
                'carnet' => '87654321',
                'licencia_conducir' => 'B67890',
                'fecha_nacimiento' => '1990-07-22',
                'direccion' => 'Calle Monseñor Rivero 456',
                'email' => 'luis.martinez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.783083,
                'ubicacion_longitud' => -63.180147,
                'estado' => 'activo',
                'tipo' => 'recogo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Garcia',
                'carnet' => '11223344',
                'licencia_conducir' => 'C12345',
                'fecha_nacimiento' => '1988-09-10',
                'direccion' => 'Av. Santos Dumont',
                'email' => 'ana.garcia@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.791610,
                'ubicacion_longitud' => -63.171720,
                'estado' => 'activo',
                'tipo' => 'recogo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Lopez',
                'carnet' => '22334455',
                'licencia_conducir' => 'D23456',
                'fecha_nacimiento' => '1987-08-12',
                'direccion' => 'Radial 27 y 1/2',
                'email' => 'carlos.lopez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.764589,
                'ubicacion_longitud' => -63.189774,
                'estado' => 'activo',
                'tipo' => 'recogo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Maria',
                'apellido' => 'Rodriguez',
                'carnet' => '33445566',
                'licencia_conducir' => 'E34567',
                'fecha_nacimiento' => '1992-03-21',
                'direccion' => 'Barrio Equipetrol',
                'email' => 'maria.rodriguez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.771079,
                'ubicacion_longitud' => -63.196689,
                'estado' => 'activo',
                'tipo' => 'recogo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
