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


        $transportes = Transporte::all()->filter(function ($transporte) {
            return $transporte->conductor->tipo == "recogo";
        });


        foreach ($transportes as $transporte) {
            $cargas = CargaOferta::where('estado', 'pendiente')->get();

            $lat_mi = $transporte->conductor->ubicacion_latitud;
            $lon_mi = $transporte->conductor->ubicacion_longitud;

            $lat_acopio = -17.750000; //env('ACOPIO_LAT');
            $lon_acopio = -63.100000; // env('ACOPIO_LON');

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

                    $locations = [];
                    //$locations[] = ['lat' => $lat_mi, 'lon' => $lon_mi];

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
                            'cantidad' => $carga->pesokg - $carga->cantidad_i,
                            'estado' => 'activo',
                            'distancia' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]]);

                        $carga->update(['estado' => 'asignado']);
                        $rutaOferta->capacidad_utilizada += $carga->pesokg - $carga->cantidad_i;
                        $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                        $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
                        $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                    }
                    $rutaOferta->save();
                    $deviceToken = $transporte->conductor->tokendevice;
                    $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];
                    if ($deviceToken) {
                        $data = [
                            'id' => json_encode($rutaOferta->id),
                            'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                        ];
                        Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                    }
                } elseif ($cargasQueCumplen->count() == 1) {
                    //echo "Existe una sola carga que satisface la capacidad por completo o casi por completo", PHP_EOL;
                    $locations = [];
                    //$locations[] = ['lat' => $lat_mi, 'lon' => $lon_mi];
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
                            'cantidad' => $carga->pesokg - $carga->cantidad_i,
                            'estado' => 'activo',
                            'distancia' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]]);
                        $carga->update(['estado' => 'asignado']);
                        $rutaOferta->capacidad_utilizada += $carga->pesokg - $carga->cantidad_i;
                        $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                        $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
                        $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                    }

                    $rutaOferta->save();

                    $deviceToken = $transporte->conductor->tokendevice;
                    $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];
                    //echo print_r($locations, true);
                    if ($deviceToken) {
                        $data = [
                            'id' => json_encode($rutaOferta->id),
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
                        //$locations[] = ['lat' => $lat_mi, 'lon' => $lon_mi];
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
                                'cantidad' => $carga->pesokg - $carga->cantidad_i,
                                'estado' => 'activo',
                                'distancia' => 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]]);
                            $carga->update(['estado' => 'asignado']);
                            $rutaOferta->capacidad_utilizada +=  $carga->pesokg - $carga->cantidad_i;
                            $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                            $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
                            $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                        }
                        $rutaOferta->save();

                        $deviceToken = $transporte->conductor->tokendevice;
                        $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];
                        //echo print_r($locations, true);
                        if ($deviceToken) {
                            $data = [
                                'id' => json_encode($rutaOferta->id),
                                'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                            ];

                            Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
                        }
                    } else {

                        /**
                         * *CARGAS IGUALES QUE ENTRE TODAS SUPERAN EL EL 70% DE LA CAPACIDAD DEL TRANSPORTE
                         * *BUSCAR UNA CARGA ADICIONAL CERCANA A ESTA  PARA PODER COMPLETAR  DE LLENAR EL TRANSPORTE
                         */



                        $cargasRuta = Utils::getCargasRuta($cargas, $radio, $lat_centro, $lon_centro, $pesoMax);


                        $locations = [];
                        //$locations[] = ['lat' => $lat_mi, 'lon' => $lon_mi];
                        $rutaOferta  = RutaOferta::create([
                            'fecha_recogida' => Carbon::now(),
                            'capacidad_utilizada' => 0,
                            'distancia_total' => 0,
                            'estado' => 'activo'
                        ]);

                        foreach ($cargasRuta as $carga) {
                            RutaCargaOferta::insert([[
                                'id_carga_oferta' => $carga->id,
                                'id_ruta_oferta' => $rutaOferta->id,
                                'id_transporte' => $transporte->id,
                                'orden' => 1,
                                'cantidad' => $carga->pesokg - $carga->cantidad_i,
                                'estado' => 'activo',
                                'distancia' => 0,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]]);
                            $carga->update(['estado' => 'asignado']);
                            $rutaOferta->capacidad_utilizada += $carga->pesokg - $carga->cantidad_i;
                            $latCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_latitud;
                            $lonCarga = $carga->ofertaDetalle->produccion->terreno->ubicacion_longitud;
                            $locations[] = ['lat' => $latCarga, 'lon' => $lonCarga];
                        }
                        $rutaOferta->save();

                        $deviceToken = $transporte->conductor->tokendevice;
                        $locations[] = ['lat' => $lat_acopio, 'lon' => $lon_acopio];

                        if ($deviceToken) {
                            $data = [
                                'id' => json_encode($rutaOferta->id),
                                'locations' => json_encode($locations), // Convertir las ubicaciones a JSON
                            ];

                            Utils::sendFcmNotificationWithLocations($deviceToken, "Ruta Asignada", "Haz click para ver.", $data, 2);
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