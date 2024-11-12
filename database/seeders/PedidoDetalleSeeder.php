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
                'cantidad' => 3,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 1,
                'id_producto' => 2,
                'id_unidadmedida' => 2,
                'cantidad' => 5,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 2,
                'id_producto' => 3,
                'id_unidadmedida' => 1,
                'cantidad' => 2,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 2,
                'id_producto' => 1,
                'id_unidadmedida' => 2,
                'cantidad' => 4,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 3,
                'id_producto' => 2,
                'id_unidadmedida' => 1,
                'cantidad' => 6,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 3,
                'id_producto' => 3,
                'id_unidadmedida' => 2,
                'cantidad' => 1,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 4,
                'id_producto' => 1,
                'id_unidadmedida' => 1,
                'cantidad' => 7,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 4,
                'id_producto' => 2,
                'id_unidadmedida' => 2,
                'cantidad' => 3,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 5,
                'id_producto' => 3,
                'id_unidadmedida' => 1,
                'cantidad' => 5,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pedido' => 5,
                'id_producto' => 1,
                'id_unidadmedida' => 2,
                'cantidad' => 4,
                'cantidad_ofertada' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
