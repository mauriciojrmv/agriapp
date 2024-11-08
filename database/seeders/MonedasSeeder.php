<?php

namespace Database\Seeders;

use App\Models\Moneda;
use Illuminate\Database\Seeder;

class MonedasSeeder extends Seeder
{
    public function run()
    {
        Moneda::insert([
            ['nombre' => 'Boliviano', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Dólar', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Euro', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
