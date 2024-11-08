<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    public function run()
    {
        Categoria::insert([
            ['nombre' => 'Frutas', 'descripcion' => 'Categoría de frutas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Verduras', 'descripcion' => 'Categoría de verduras', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cereales', 'descripcion' => 'Categoría de cereales', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
