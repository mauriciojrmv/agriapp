<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CargaPedidoSeeder extends Seeder
{
    public function run()
    {
        $pedidoDetalles = DB::table('pedido_detalles')->get();

        if ($pedidoDetalles->isEmpty()) {
            $this->command->info('No hay detalles de pedidos para generar cargas.');
            return;
        }

        foreach ($pedidoDetalles as $detalle) {
            // Verifica si ya existe una carga para este detalle de pedido
            $cargaExistente = DB::table('carga_pedidos')
                ->where('id_pedido_detalle', $detalle->id)
                ->exists();

            if ($cargaExistente) {
                $this->command->info("La carga ya existe para el detalle de pedido ID: {$detalle->id}. Saltando...");
                continue;
            }

            // Crear una sola carga con la cantidad completa
            DB::table('carga_pedidos')->insert([
                [
                    'id_pedido_detalle' => $detalle->id,
                    'cantidad' => $detalle->cantidad, // Cantidad completa sin dividir
                    'estado' => 'recogida',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ]);
        }

        $this->command->info('Cargas de pedidos generadas exitosamente.');
    }
}
