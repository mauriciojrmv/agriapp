<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PedidoDetalleSeeder extends Seeder
{
    public function run()
    {
        DB::table('pedido_detalles')->insert([
            [
                'id_pedido' => 1,
                'id_producto' => 1,
                'id_unidadmedida' => 1,
                'cantidad' => 500,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 1,
                'id_producto' => 2,
                'id_unidadmedida' => 2,
                'cantidad' => 600,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 2,
                'id_producto' => 3,
                'id_unidadmedida' => 1,
                'cantidad' => 500,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 2,
                'id_producto' => 1,
                'id_unidadmedida' => 2,
                'cantidad' => 1000,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 3,
                'id_producto' => 2,
                'id_unidadmedida' => 1,
                'cantidad' => 1500,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 3,
                'id_producto' => 3,
                'id_unidadmedida' => 2,
                'cantidad' => 1000,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 4,
                'id_producto' => 1,
                'id_unidadmedida' => 1,
                'cantidad' => 800,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 4,
                'id_producto' => 2,
                'id_unidadmedida' => 2,
                'cantidad' => 300,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 5,
                'id_producto' => 3,
                'id_unidadmedida' => 1,
                'cantidad' => 350,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 5,
                'id_producto' => 1,
                'id_unidadmedida' => 2,
                'cantidad' => 600,
                'cantidad_ofertada' => 0,
                'estado_ofertado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
