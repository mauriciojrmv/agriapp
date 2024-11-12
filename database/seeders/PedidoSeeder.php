<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PedidoSeeder extends Seeder
{
    public function run()
    {
        DB::table('pedidos')->insert([
            [
                'id_cliente' => 1,
                'fecha_entrega' => '2024-11-15',
                'ubicacion_longitud' => '-63.1805',
                'ubicacion_latitud' => '-17.7800',
                'estado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_cliente' => 2,
                'fecha_entrega' => '2024-11-17',
                'ubicacion_longitud' => '-63.1822',
                'ubicacion_latitud' => '-17.7811',
                'estado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_cliente' => 3,
                'fecha_entrega' => '2024-11-20',
                'ubicacion_longitud' => '-63.1850',
                'ubicacion_latitud' => '-17.7822',
                'estado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_cliente' => 1,
                'fecha_entrega' => '2024-11-22',
                'ubicacion_longitud' => '-63.1875',
                'ubicacion_latitud' => '-17.7833',
                'estado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_cliente' => 2,
                'fecha_entrega' => '2024-11-25',
                'ubicacion_longitud' => '-63.1890',
                'ubicacion_latitud' => '-17.7844',
                'estado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
