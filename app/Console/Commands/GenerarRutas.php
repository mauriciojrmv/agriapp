<?php

namespace App\Console\Commands;

use App\Models\CargaOferta;
use Illuminate\Console\Command;
use App\Helpers\Utils;
use App\Models\RutaCargaOferta;
use App\Models\RutaOferta;
use App\Models\Transporte;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Break_;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class GenerarRutas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generar:rutas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para generar Rutas de acuerdo a las cargas disponibles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * * DATOS NECESARIO
         * * UBICACION FINAL (PUNTO DE ACOPIO DEFINIDO)
         * * UBICACION INICIAL (PUNTO DE UBICACION DEL CONDUCTOR)
         * * UBICACION DE LAS CARGAS (PUNTOS DE UBICACION)
         * * AGRUPAR 
         * * GENERAR LA RUTA PARA ESE CONDUCTOR DE 
         */


        $transportes = Transporte::all();


        foreach ($transportes as $transporte) {
            $cargas = CargaOferta::where('estado', 'pendiente')->get();

            $lat_mi = $transporte->conductor->ubicacion_latitud;
            $lon_mi = $transporte->conductor->ubicacion_longitud;
         
            $lat_acopio =-17.750000;//env('ACOPIO_LAT');
            $lon_acopio = -63.100000;// env('ACOPIO_LON');
  
            $lat_centro = ($lat_mi + $lat_acopio) / 2;
            $lon_centro = ($lon_mi + $lon_acopio) / 2;
            $radio = Utils::haversine($lat_mi, $lon_mi, $lat_acopio, $lon_acopio);
            $pesoMax = $transporte->capacidadmaxkg;
            $cargaMasCercana = Utils::buscarCargaCercana($cargas, $radio, $lat_centro, $lon_centro);

            if ($cargaMasCercana) {
                $idOfertaDetalle =  $cargaMasCercana->id_oferta_detalle;
                $cargasConMismoIdOferta = Utils::getCargasMismaIdOfertaDetalle($cargas, $idOfertaDetalle);
                $cargasQueCumplen = Utils::getCargaSatisfacenAltransporteC($cargasConMismoIdOferta, $pesoMax);


                /**
                 * * EXISTE MAS DE UNA SOLA CARGA QUE LLENA TODA LA CAPACIDAD
                 */
                if ($cargasQueCumplen->count() > 1) {
                    //echo "Existe mas de una sola carga que satisface la capacidad por completo o casi por completo", PHP_EOL;
                    //echo "Cantidad: " . $cargasQueCumplen->count(), PHP_EOL;
                    $locations = [];
                    $locations[] = ['lat' => $lat_mi, 'lon' => $lon_mi];

                    $rutaOferta  = RutaOferta::create([
                        'fecha_recogida' => Carbon::now(),
                        'capacidad_utilizada' => 0,
                        'distancia_total' => 0,
                        'estado' => 'activo'
                    ]);
                    $sw = true;
                    foreach ($cargasQueCumplen as $carga) {
             
                            RutaCargaOferta::insert([[
                                'id_carga_oferta' => $carga->id,
                                'id_ruta_oferta' => $rutaOferta->id,
                                'id_transporte' => $transporte->id,
                                'orden' => 1,
                                'estado' => 'activo',
                                'distancia' => 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]]);

                           // $carga->update(['estado' => 'asignado']);
                            $rutaOferta->capacidad_utilizada = $carga->pesokg;
                            $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                            $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
                            $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                      
                        
                    }

                    $rutaOferta->save();
                    //$deviceToken = 'd9DDEyr4T_unqGNlo-5BB-:APA91bE1QTpbGgqItZ0DLgk7qYkVeAwv-MSqDgwN5SZHCGIw7uQWVwW-WV1ygO8R3UKz8Bl5bntRl2sQvRoTiJB68tp8as4ZbPrwN-F80ozch8yM2lOfkvc';
                    $deviceToken = $transporte->conductor->tokendevice;
                    $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];
                    echo print_r($locations, true);
                    if ($deviceToken) {
                        $data = [
                            'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                        ];
                        Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                    }
                } elseif ($cargasQueCumplen->count() == 1) {
                    //echo "Existe una sola carga que satisface la capacidad por completo o casi por completo", PHP_EOL;
                    $locations = [];
                    $locations[] = ['lat' => $lat_mi, 'lon' => $lon_mi];
                    $rutaOferta  = RutaOferta::create([
                        'fecha_recogida' => Carbon::now(),
                        'capacidad_utilizada' => 0,
                        'distancia_total' => 0,
                        'estado' => 'activo'
                    ]);

                    foreach ($cargasQueCumplen as $carga) {
                        RutaCargaOferta::insert([[
                            'id_carga_oferta' => $carga->id,
                            'id_ruta_oferta' => $rutaOferta->id,
                            'id_transporte' => $transporte->id,
                            'orden' => 1,
                            'estado' => 'activo',
                            'distancia' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]]);
                        //$carga->update(['estado' => 'asignado']);
                        $rutaOferta->capacidad_utilizada = $carga->pesokg;
                        $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                        $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
                        $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                    }

                    $rutaOferta->save();
                    //$deviceToken = 'd9DDEyr4T_unqGNlo-5BB-:APA91bE1QTpbGgqItZ0DLgk7qYkVeAwv-MSqDgwN5SZHCGIw7uQWVwW-WV1ygO8R3UKz8Bl5bntRl2sQvRoTiJB68tp8as4ZbPrwN-F80ozch8yM2lOfkvc';
                    $deviceToken = $transporte->conductor->tokendevice;
                    $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];
                    echo print_r($locations, true);
                    if ($deviceToken) {
                        $data = [
                            'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                        ];
                        Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                    }
                } elseif ($cargasQueCumplen->count() == 0) {
                    //echo "No Existe una sola carga que satisface la capacidad por completo o casi por completo", PHP_EOL;
                    $sumcargasQueCumplen = Utils::getCargasSatisfacenAltransporte($cargasConMismoIdOferta, $pesoMax);
                    $mas10 = $pesoMax + ($pesoMax * 10 / 100);
                    $menos10 = $pesoMax - ($pesoMax * 10 / 100);
                    $sumaPesoKg = $sumcargasQueCumplen->sum('pesokg');
                    if ($sumaPesoKg >= $menos10 && $sumaPesoKg <= $mas10) {
                        $locations = [];
                        $locations[] = ['lat' => $lat_mi, 'lon' => $lon_mi];
                        $rutaOferta  = RutaOferta::create([
                            'fecha_recogida' => Carbon::now(),
                            'capacidad_utilizada' => 0,
                            'distancia_total' => 0,
                            'estado' => 'activo'
                        ]);

                        foreach ($sumcargasQueCumplen as $carga) {
                            RutaCargaOferta::insert([[
                                'id_carga_oferta' => $carga->id,
                                'id_ruta_oferta' => $rutaOferta->id,
                                'id_transporte' => $transporte->id,
                                'orden' => 1,
                                'estado' => 'activo',
                                'distancia' => 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]]);
                           // $carga->update(['estado' => 'asignado']);
                            $rutaOferta->capacidad_utilizada += $carga->pesokg;
                            $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                            $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
                            $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                        }
                        $rutaOferta->save();


                        // $deviceToken = 'd9DDEyr4T_unqGNlo-5BB-:APA91bE1QTpbGgqItZ0DLgk7qYkVeAwv-MSqDgwN5SZHCGIw7uQWVwW-WV1ygO8R3UKz8Bl5bntRl2sQvRoTiJB68tp8as4ZbPrwN-F80ozch8yM2lOfkvc';
                        $deviceToken = $transporte->conductor->tokendevice;
                        $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];
                        echo print_r($locations, true);
                        if ($deviceToken) {
                            $data = [
                                'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                            ];
                            
                            Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                        }
                    } else {
                        $peso70 = $pesoMax - ($pesoMax * 40 / 100);
                        $peso50 = $pesoMax - ($pesoMax * 50 / 100);
                        if ($sumaPesoKg >= $peso50 && $sumaPesoKg <= $peso70) {
                            /**
                             * *CARGAS IGUALES QUE POR LO MENOS CUMPLE CON ENTRE 50 Y 70 % DE LA CAPACIDAD DEL TRANSPORTE
                             */
                        } else {
                            /**
                             * *CARGAS IGUALES QUE ENTRE TODAS SUPERAN EL EL 70% DE LA CAPACIDAD DEL TRANSPORTE
                             * *BUSCAR UNA CARGA ADICIONAL CERCANA A ESTA  PARA PODER COMPLETAR  DE LLENAR EL TRANSPORTE
                             */



                            $cargasRuta = Utils::getCargasRuta($cargas, $radio, $lat_centro, $lon_centro);





                            /* $data = [];
                            echo "Punto de Partida: " . $lat_mi . " " . $lon_mi, PHP_EOL;
                            echo "Punto de Llegada: " . $lat_acopio . " " . $lon_acopio, PHP_EOL;

                            foreach ($cargasRuta as $carga) {
                                $data[] = [
                                    $carga->id,
                                    $carga->id_oferta_detalle,
                                    $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud,
                                    $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud,
                                    $carga->pesokg,
                                ];
                            }

                            // Crear la salida en consola
                            $output = new ConsoleOutput();
                            $table = new Table($output);

                            // Definir encabezados y filas
                            $table
                                ->setHeaders(['id', 'id_oferta_detalle', 'lat', 'lon',  'pesokg'])
                                ->setRows($data);

                            // Renderizar la tabla
                            $table->render(); */
                            /* echo "Peso capacidad del Transporte: " . $pesoMax, PHP_EOL;
                        echo "Peso capacidad 70: " . $peso70, PHP_EOL;
                        echo "Peso capacidad 50: " . $peso50, PHP_EOL;
                        echo "Peso de la Carga: " . $sumaPesoKg, PHP_EOL;
                        echo $transporte->modelo, PHP_EOL;
                        echo "Ni la suma de todas las cargas con la misma IdOferta cumplen", PHP_EOL; */
                        }
                    }
                }
            }
        }
    }
}


/*         $data = [];
                foreach ($cargasQueCumplen as $carga) {
                    $data[] = [
                        $carga->id,
                        $carga->id_oferta_detalle,
                        $carga->pesokg,
                    ];
                }

                // Crear la salida en consola
                $output = new ConsoleOutput();
                $table = new Table($output);

                // Definir encabezados y filas
                $table
                    ->setHeaders(['id', 'id_oferta_detalle', 'pesokg'])
                    ->setRows($data);

                // Renderizar la tabla
                $table->render(); */