<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RutaCargaPedidoSeeder extends Seeder
{
    public function run()
    {
        $puntoAcopioLat = -17.750000; // Coordenadas del punto de acopio
        $puntoAcopioLon = -63.100000;

        // Función para calcular la distancia entre dos puntos
        $calcularDistancia = function ($lat1, $lon1, $lat2, $lon2) {
            $radioTierra = 6371; // Radio de la Tierra en km

            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                sin($dLon / 2) * sin($dLon / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            return $radioTierra * $c; // Distancia en km
        };

        // Obtener todas las cargas pendientes
        $cargasPendientes = DB::table('carga_pedidos')->where('estado', 'recogida')->get();

        if ($cargasPendientes->isEmpty()) {
            $this->command->info('No hay cargas pendientes para generar rutas.');
            return;
        }

        // Obtener todas las rutas de pedidos pendientes
        $rutaPedidos = DB::table('ruta_pedidos')->where('estado', 'recogida')->get();

        foreach ($rutaPedidos as $ruta) {
            $capacidadRestante = $ruta->capacidad_utilizada; // Capacidad inicial de la ruta
            $orden = 1;

            foreach ($cargasPendientes as $carga) {
                // Verificar si esta carga ya está asignada completamente
                $asignada = DB::table('ruta_carga_pedidos')
                    ->where('id_carga_pedido', $carga->id)
                    ->exists();

                if ($asignada) {
                    continue; // Saltar si ya está asignada
                }

                // Obtener el detalle del pedido asociado a esta carga
                $pedidoDetalle = DB::table('pedido_detalles')->where('id', $carga->id_pedido_detalle)->first();
                $pedido = DB::table('pedidos')->where('id', $pedidoDetalle->id_pedido)->first();

                // Calcular la distancia desde el punto de acopio hasta la ubicación del pedido
                $distancia = $calcularDistancia(
                    $puntoAcopioLat,
                    $puntoAcopioLon,
                    $pedido->ubicacion_latitud,
                    $pedido->ubicacion_longitud
                );

                // Validar si la carga puede ser asignada a esta ruta
                if ($capacidadRestante >= $carga->cantidad) {
                    DB::table('ruta_carga_pedidos')->insert([
                        'id_carga_pedido' => $carga->id,
                        'id_ruta_pedido' => $ruta->id,
                        'id_transporte' => rand(1, 5), // Transporte aleatorio
                        'orden' => $orden,
                        'estado' => 'recogida',
                        'distancia' => $distancia,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    // Reducir la capacidad restante de la ruta
                    $capacidadRestante -= $carga->cantidad;

                    // Incrementar el orden
                    $orden++;
                }
            }
        }

        $this->command->info('Rutas de carga para pedidos generadas exitosamente.');
    }
}
