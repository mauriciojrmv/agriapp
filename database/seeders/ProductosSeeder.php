<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run()
    {
        Producto::insert([
            ['id_categoria' => 1, 'nombre' => 'Manzana', 'descripcion' => 'Fruta fresca', 'created_at' => now(), 'updated_at' => now()],
            ['id_categoria' => 2, 'nombre' => 'Lechuga', 'descripcion' => 'Verdura verde', 'created_at' => now(), 'updated_at' => now()],
            ['id_categoria' => 3, 'nombre' => 'Arroz', 'descripcion' => 'Cereal básico', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
