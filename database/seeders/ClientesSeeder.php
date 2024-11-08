<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    public function run()
    {
        Cliente::insert([
            [
                'nombre' => 'Maria',
                'apellido' => 'Lopez',
                'email' => 'maria.lopez@example.com',
                'telefono' => '76543211',
                'password' => bcrypt('password123'),
                'direccion' => 'Calle 1 #101',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Jose',
                'apellido' => 'Rodriguez',
                'email' => 'jose.rodriguez@example.com',
                'telefono' => '76543212',
                'password' => bcrypt('password123'),
                'direccion' => 'Calle 2 #102',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Elena',
                'apellido' => 'Fernandez',
                'email' => 'elena.fernandez@example.com',
                'telefono' => '76543213',
                'password' => bcrypt('password123'),
                'direccion' => 'Calle 3 #103',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
