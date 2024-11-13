<?php

namespace Database\Seeders;

use App\Models\Agricultor;
use Illuminate\Database\Seeder;

class AgricultorsSeeder extends Seeder
{
    public function run()
    {
        Agricultor::insert([
            [
                'nombre' => 'Carlos',
                'apellido' => 'Ramos',
                'telefono' => '76543210',
                'email' => 'carlos.ramos@example.com',
                'direccion' => 'Zona Central #123',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco ABC - 123456',
                'nit' => '123456789',
                'carnet' => '98765432',
                'licencia_funcionamiento' => 'LF12345',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Lucia',
                'apellido' => 'Mendez',
                'telefono' => '76543123',
                'email' => 'lucia.mendez@example.com',
                'direccion' => 'Zona Sur #456',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco XYZ - 654321',
                'nit' => '987654321',
                'carnet' => '87654320',
                'licencia_funcionamiento' => 'LF54321',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Gomez',
                'telefono' => '76543456',
                'email' => 'pedro.gomez@example.com',
                'direccion' => 'Zona Norte #789',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco DEF - 987654',
                'nit' => '564738291',
                'carnet' => '12345678',
                'licencia_funcionamiento' => 'LF67891',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Lopez',
                'telefono' => '76543567',
                'email' => 'ana.lopez@example.com',
                'direccion' => 'Zona Oeste #321',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco GHI - 876543',
                'nit' => '342156789',
                'carnet' => '56789013',
                'licencia_funcionamiento' => 'LF34567',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Miguel',
                'apellido' => 'Perez',
                'telefono' => '76543678',
                'email' => 'miguel.perez@example.com',
                'direccion' => 'Zona Este #654',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco JKL - 135792',
                'nit' => '975318642',
                'carnet' => '87654322',
                'licencia_funcionamiento' => 'LF89012',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Sofia',
                'apellido' => 'Jimenez',
                'telefono' => '76543789',
                'email' => 'sofia.jimenez@example.com',
                'direccion' => 'Zona Centro #987',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco MNO - 246813',
                'nit' => '192837465',
                'carnet' => '34567891',
                'licencia_funcionamiento' => 'LF45678',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Juan',
                'apellido' => 'Gutierrez',
                'telefono' => '76543890',
                'email' => 'juan.gutierrez@example.com',
                'direccion' => 'Zona Sur #123',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco PQR - 789456',
                'nit' => '456123789',
                'carnet' => '78901235',
                'licencia_funcionamiento' => 'LF90123',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Salazar',
                'telefono' => '76543901',
                'email' => 'laura.salazar@example.com',
                'direccion' => 'Zona Norte #456',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco STU - 963852',
                'nit' => '654789123',
                'carnet' => '12345679',
                'licencia_funcionamiento' => 'LF23456',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Jose',
                'apellido' => 'Fernandez',
                'telefono' => '76543012',
                'email' => 'jose.fernandez@example.com',
                'direccion' => 'Zona Este #789',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco VWX - 321654',
                'nit' => '789456123',
                'carnet' => '23456780',
                'licencia_funcionamiento' => 'LF56789',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Carmen',
                'apellido' => 'Ortega',
                'telefono' => '76543123',
                'email' => 'carmen.ortega@example.com',
                'direccion' => 'Zona Oeste #159',
                'password' => bcrypt('password123'),
                'informacion_bancaria' => 'Banco YZA - 147258',
                'nit' => '321654987',
                'carnet' => '45678902',
                'licencia_funcionamiento' => 'LF67892',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
