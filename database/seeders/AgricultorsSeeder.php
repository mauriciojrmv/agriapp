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
                'carnet' => '87654321',
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
                'licencia_funcionamiento' => 'LF67890',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
