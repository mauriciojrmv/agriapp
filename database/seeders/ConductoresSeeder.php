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
                'ubicacion_latitud' => -17.510727,
                'ubicacion_longitud' => -63.172123,
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
                'ubicacion_latitud' => -17.338007,
                'ubicacion_longitud' => -63.257834,
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
                'ubicacion_latitud' => -17.848766,
                'ubicacion_longitud' => -63.225896,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Lopez',
                'carnet' => '22334455',
                'licencia_conducir' => 'D23456',
                'fecha_nacimiento' => '1987-08-12',
                'direccion' => 'Av. Norte #350',
                'email' => 'carlos.lopez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.747318,
                'ubicacion_longitud' => -63.094204,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Maria',
                'apellido' => 'Rodriguez',
                'carnet' => '33445566',
                'licencia_conducir' => 'E34567',
                'fecha_nacimiento' => '1992-03-21',
                'direccion' => 'Zona Sur Calle 3',
                'email' => 'maria.rodriguez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.986115,
                'ubicacion_longitud' => -63.365089,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Gomez',
                'carnet' => '44556677',
                'licencia_conducir' => 'F45678',
                'fecha_nacimiento' => '1993-06-18',
                'direccion' => 'Calle Oeste #10',
                'email' => 'pedro.gomez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.800951,
                'ubicacion_longitud' => -63.479062,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Lucia',
                'apellido' => 'Fernandez',
                'carnet' => '55667788',
                'licencia_conducir' => 'G56789',
                'fecha_nacimiento' => '1995-02-14',
                'direccion' => 'Zona Norte Calle 4',
                'email' => 'lucia.fernandez@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.458926,
                'ubicacion_longitud' => -63.666389,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Miguel',
                'apellido' => 'Ruiz',
                'carnet' => '66778899',
                'licencia_conducir' => 'H67890',
                'fecha_nacimiento' => '1989-11-30',
                'direccion' => 'Zona Sur Calle 5',
                'email' => 'miguel.ruiz@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.656453,
                'ubicacion_longitud' => -62.814606,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Salas',
                'carnet' => '77889900',
                'licencia_conducir' => 'I78901',
                'fecha_nacimiento' => '1991-09-09',
                'direccion' => 'Zona Oeste Calle 7',
                'email' => 'laura.salas@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.401688,
                'ubicacion_longitud' => -63.828387,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Diego',
                'apellido' => 'Ramos',
                'carnet' => '88990011',
                'licencia_conducir' => 'J89012',
                'fecha_nacimiento' => '1996-12-19',
                'direccion' => 'Zona Norte Calle 6',
                'email' => 'diego.ramos@example.com',
                'password' => bcrypt('password123'),
                'ubicacion_latitud' => -17.777920,
                'ubicacion_longitud' => -62.897094,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
