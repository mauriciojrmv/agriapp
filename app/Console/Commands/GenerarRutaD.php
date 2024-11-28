<?php

namespace App\Console\Commands;

use App\Helpers\Utils;
use App\Models\CargaPedido;
use App\Models\Conductor;
use App\Models\RutaCargaPedido;
use App\Models\RutaPedido;
use App\Models\Transporte;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class GenerarRutaD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generar:rutasd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $transportes = Transporte::all()->filter(function ($transporte) {
            return $transporte->conductor->tipo == "delivery";
        });




        foreach ($transportes as $transporte) {
            $cargas = CargaPedido::where('estado', 'recogida')->get();
            /**
             * *CARGA QUE SATISFACE POR COMPLETO
             */
            $lat_acopio = -17.750000; //env('ACOPIO_LAT');
            $lon_acopio = -63.100000; // env('ACOPIO_LON');
            $pesoMax = $transporte->capacidadmaxkg;
            //echo $pesoMax, PHP_EOL;
            $cargaQueCumple = Utils::getCargaCompleta($cargas, $pesoMax);
            if ($cargaQueCumple) {
                echo $cargaQueCumple->id, PHP_EOL;
                $locations = [];
                $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];

                $rutaPedido  = RutaPedido::create([
                    'fecha_entrega' => Carbon::now(),
                    'capacidad_utilizada' => 0,
                    'distancia_total' => 0,
                    'estado' => 'activo'
                ]);


                RutaCargaPedido::insert([[
                    'id_carga_pedido' => $cargaQueCumple->id,
                    'id_ruta_pedido' => $rutaPedido->id,
                    'id_transporte' => $transporte->id,
                    'orden' => 1,
                    'cantidad' => $cargaQueCumple->cantidad - $cargaQueCumple->cantidad_i,
                    'estado' => 'activo',
                    'distancia' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]]);

                $cargaQueCumple->update(['estado' => 'asignado']);
                $rutaPedido->capacidad_utilizada = $cargaQueCumple->cantidad - $cargaQueCumple->cantidad_i;
                $latCarga = $cargaQueCumple->pedidoDetalle->pedido->ubicacion_latitud;
                $lonCarga = $cargaQueCumple->pedidoDetalle->pedido->ubicacion_longitud;
                $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];


                $rutaPedido->save();

                $deviceToken = $transporte->conductor->tokendevice;

                if ($deviceToken) {
                    $data = [
                        'ruta_id:' => $rutaPedido->id,
                        'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                    ];
                    Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                }
            } else {
                /**
                 * * CARGAS SUMADAS SATISFACEN AL TRANSPORTE
                 */
                $cargasSatisfacen = Utils::getCargasSatisfacenC($cargas, $pesoMax);

                if ($cargasSatisfacen->count() > 1) {
                    $locations = [];
                    $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];

                    $rutaPedido  = RutaPedido::create([
                        'fecha_entrega' => Carbon::now(),
                        'capacidad_utilizada' => 0,
                        'distancia_total' => 0,
                        'estado' => 'activo'
                    ]);

                    foreach ($cargasSatisfacen as $carga) {

                        RutaCargaPedido::insert([[
                            'id_carga_pedido' => $carga->id,
                            'id_ruta_pedido' => $rutaPedido->id,
                            'id_transporte' => $transporte->id,
                            'orden' => 1,
                            'cantidad' => $carga->cantidad - $carga->cantidad_i,
                            'estado' => 'activo',
                            'distancia' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]]);

                        $carga->update(['estado' => 'asignado']);
                        $rutaPedido->capacidad_utilizada += $carga->cantidad - $carga->cantidad_i;
                        $latCarga = $carga->pedidoDetalle->pedido->ubicacion_latitud;
                        $lonCarga = $carga->pedidoDetalle->pedido->ubicacion_longitud;
                        $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                    }

                    $rutaPedido->save();

                    $deviceToken = $transporte->conductor->tokendevice;

                    if ($deviceToken) {
                        $data = [
                            'ruta_id:' => $rutaPedido->id,
                            'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                        ];
                        Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                    }
                } else {
                    /**
                     * * LAS CARGAS SON MUY GRANDES PARA EL TRANSPORTE BUSCAR TRANSPORTES PARA QUE LLEVEN LAS CARGAS POR SEPARADO
                     */

                    $carga = $cargas->first();

                    // $cargaCompartida = Utils::buscarCargaCercana();

                    $locations = [];
                    $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];

                    $rutaPedido  = RutaPedido::create([
                        'fecha_entrega' => Carbon::now(),
                        'capacidad_utilizada' => 0,
                        'distancia_total' => 0,
                        'estado' => 'activo'
                    ]);


                    RutaCargaPedido::insert([[
                        'id_carga_pedido' => $carga->id,
                        'id_ruta_pedido' => $rutaPedido->id,
                        'id_transporte' => $transporte->id,
                        //Utilizando orden como cantidad
                        'orden' => 1,
                        'cantidad' => $pesoMax,
                        'estado' => 'activo',
                        'distancia' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]]);

                    $carga->update(['estado' => 'recogida', 'cantidad_i' => $pesoMax,]);
                    $rutaPedido->capacidad_utilizada = $pesoMax;
                    $latCarga = $carga->pedidoDetalle->pedido->ubicacion_latitud;
                    $lonCarga = $carga->pedidoDetalle->pedido->ubicacion_longitud;
                    $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];

                    $rutaPedido->save();

                    $deviceToken = $transporte->conductor->tokendevice;

                    if ($deviceToken) {
                        $data = [
                            'ruta_id:' => $rutaPedido->id,
                            'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                        ];
                        Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                    }
                }
            }
        }
    }
}
