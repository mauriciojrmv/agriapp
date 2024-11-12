<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfertaDetalleSeeder extends Seeder
{
    public function run()
    {
        DB::table('oferta_detalles')->insert([
            [
                'id_oferta' => 1,
                'id_produccion' => 1, // Valor de id_produccion correspondiente
                'id_unidadmedida' => 1,
                'id_moneda' => 1,
                'descripcion' => 'Detalle de oferta 1',
                'cantidad_fisico' => 34,
                'cantidad_comprometido' => 0,
                'precio' => 103.52,
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 2,
                'id_produccion' => 2,
                'id_unidadmedida' => 2,
                'id_moneda' => 2,
                'descripcion' => 'Detalle de oferta 2',
                'cantidad_fisico' => 90,
                'cantidad_comprometido' => 0,
                'precio' => 58.36,
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 3,
                'id_produccion' => 3,
                'id_unidadmedida' => 2,
                'id_moneda' => 2,
                'descripcion' => 'Detalle de oferta 3',
                'cantidad_fisico' => 25,
                'cantidad_comprometido' => 0,
                'precio' => 100.57,
                'estado' => 'inactivo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 4,
                'id_produccion' => 4,
                'id_unidadmedida' => 2,
                'id_moneda' => 2,
                'descripcion' => 'Detalle de oferta 4',
                'cantidad_fisico' => 64,
                'cantidad_comprometido' => 0,
                'precio' => 113.60,
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 5,
                'id_produccion' => 5,
                'id_unidadmedida' => 2,
                'id_moneda' => 1,
                'descripcion' => 'Detalle de oferta 5',
                'cantidad_fisico' => 43,
                'cantidad_comprometido' => 0,
                'precio' => 137.09,
                'estado' => 'inactivo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 6,
                'id_produccion' => 6,
                'id_unidadmedida' => 1,
                'id_moneda' => 1,
                'descripcion' => 'Detalle de oferta 6',
                'cantidad_fisico' => 56,
                'cantidad_comprometido' => 0,
                'precio' => 99.99,
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 7,
                'id_produccion' => 7,
                'id_unidadmedida' => 1,
                'id_moneda' => 1,
                'descripcion' => 'Detalle de oferta 7',
                'cantidad_fisico' => 120,
                'cantidad_comprometido' => 0,
                'precio' => 200.50,
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 8,
                'id_produccion' => 8,
                'id_unidadmedida' => 2,
                'id_moneda' => 2,
                'descripcion' => 'Detalle de oferta 8',
                'cantidad_fisico' => 75,
                'cantidad_comprometido' => 0,
                'precio' => 75.25,
                'estado' => 'inactivo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 9,
                'id_produccion' => 9,
                'id_unidadmedida' => 1,
                'id_moneda' => 1,
                'descripcion' => 'Detalle de oferta 9',
                'cantidad_fisico' => 90,
                'cantidad_comprometido' => 0,
                'precio' => 110.30,
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_oferta' => 10,
                'id_produccion' => 10,
                'id_unidadmedida' => 2,
                'id_moneda' => 2,
                'descripcion' => 'Detalle de oferta 10',
                'cantidad_fisico' => 30,
                'cantidad_comprometido' => 0,
                'precio' => 50.75,
                'estado' => 'activo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
