<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RutaPedidoSeeder extends Seeder
{
    public function run()
    {
        $cargasPendientes = DB::table('carga_pedidos')
            ->where('estado', 'recogida')
            ->get();

        if ($cargasPendientes->isEmpty()) {
            $this->command->info('No hay cargas pendientes para generar rutas.');
            return;
        }

        $rutaCounter = 1;
        $capacidadMaxima = 1000; // Capacidad máxima ajustada a un valor más realista
        $capacidadUtilizada = 0;

        $rutaId = null;

        foreach ($cargasPendientes as $carga) {
            // Obtener la información de la ubicación del pedido para calcular distancias
            $pedidoDetalle = DB::table('pedido_detalles')->where('id', $carga->id_pedido_detalle)->first();
            $pedido = DB::table('pedidos')->where('id', $pedidoDetalle->id_pedido)->first();

            // Calcula la distancia simulada entre el punto de acopio y la ubicación del pedido
            $distanciaSimulada = rand(5, 20); // Cambiar por cálculo real si se tiene un algoritmo

            // Si la capacidad actual excede el límite, crea una nueva ruta
            if ($capacidadUtilizada + $carga->cantidad > $capacidadMaxima) {
                $capacidadUtilizada = 0;
                $rutaCounter++;
            }

            // Si no hay una ruta en uso, crea una nueva
            if (!$rutaId || $capacidadUtilizada == 0) {
                $rutaId = DB::table('ruta_pedidos')->insertGetId([
                    'fecha_entrega' => Carbon::now()->addDays($rutaCounter),
                    'capacidad_utilizada' => 0,
                    'distancia_total' => 0, // Se actualizará más adelante
                    'estado' => 'en_proceso',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $this->command->info("Ruta creada: Ruta ID $rutaId");
            }

            // Relacionar la carga con la ruta creada
            DB::table('ruta_carga_pedidos')->insert([
                'id_carga_pedido' => $carga->id,
                'id_ruta_pedido' => $rutaId,
                'id_transporte' => rand(1, 5), // Asignar transporte aleatorio para pruebas
                'orden' => 1, // Ajustar lógica de orden según sea necesario
                'estado' => 'recogida',
                'distancia' => $distanciaSimulada,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Aumentar la capacidad utilizada
            $capacidadUtilizada += $carga->cantidad;

            // Actualizar la capacidad utilizada y distancia total en la tabla ruta_pedidos
            DB::table('ruta_pedidos')->where('id', $rutaId)->update([
                'capacidad_utilizada' => $capacidadUtilizada,
                'distancia_total' => DB::table('ruta_carga_pedidos')
                    ->where('id_ruta_pedido', $rutaId)
                    ->sum('distancia'),
                'updated_at' => Carbon::now(),
            ]);
        }

        $this->command->info('Rutas de pedidos generadas exitosamente.');
    }
}
