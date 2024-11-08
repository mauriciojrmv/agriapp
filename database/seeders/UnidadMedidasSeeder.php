<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidasSeeder extends Seeder
{
    public function run()
    {
        UnidadMedida::insert([
            ['nombre' => 'Kilogramo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Libra', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Arroba', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
