<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfertaSeeder extends Seeder
{
    public function run()
    {
        DB::table('ofertas')->insert([
            [
                'id_produccion' => 1,
                'fecha_creacion' => '2024-10-30',
                'fecha_expiracion' => '2025-04-06',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 2,
                'fecha_creacion' => '2024-10-14',
                'fecha_expiracion' => '2025-02-14',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 1,
                'fecha_creacion' => '2024-11-04',
                'fecha_expiracion' => '2025-01-09',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 3,
                'fecha_creacion' => '2024-10-17',
                'fecha_expiracion' => '2025-04-15',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 2,
                'fecha_creacion' => '2024-11-03',
                'fecha_expiracion' => '2025-04-26',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 1,
                'fecha_creacion' => '2024-10-20',
                'fecha_expiracion' => '2025-03-19',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 3,
                'fecha_creacion' => '2024-10-24',
                'fecha_expiracion' => '2025-02-22',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 2,
                'fecha_creacion' => '2024-11-08',
                'fecha_expiracion' => '2025-03-12',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 3,
                'fecha_creacion' => '2024-10-26',
                'fecha_expiracion' => '2025-01-20',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_produccion' => 1,
                'fecha_creacion' => '2024-10-12',
                'fecha_expiracion' => '2025-02-17',
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
