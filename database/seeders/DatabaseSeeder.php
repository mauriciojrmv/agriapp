<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AgricultorsSeeder::class,
            ClientesSeeder::class,
            ConductoresSeeder::class,
            CategoriasSeeder::class,
            TerrenosSeeder::class,
            TemporadasSeeder::class,
            ProductosSeeder::class,
            TransportesSeeder::class,
            UnidadMedidasSeeder::class,
            MonedasSeeder::class,
            // Aqui agregamos mas seeders
            ]);
    }
}
