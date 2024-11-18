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
        $cargas = CargaOferta::where('estado', 'activo')->get();

        $transportes = Transporte::all();

        /* 




        $data = [];
        foreach ($transportes as $transporte) {
            $data[] = [
                $transporte->id,
                $transporte->capacidadmaxkg,
                $transporte->conductor->ubicacion_latitud,
                $transporte->conductor->ubicacion_longitud,
            ];
        }

        // Crear la salida en consola
        $output = new ConsoleOutput();
        $table = new Table($output);

        // Definir encabezados y filas
        $table
            ->setHeaders(['id', 'capacidadmaxkg', 'ubicacion_latitud', 'ubicacion_longitud'])
            ->setRows($data);

        // Renderizar la tabla
        //$table->render();
 */

        foreach ($transportes as $transporte) {


            $lat_mi = $transporte->conductor->ubicacion_latitud;
            $lon_mi = $transporte->conductor->ubicacion_longitud;
            $pesoMax = $transporte->capacidadmaxkg;
            $cargaMasCercana = Utils::findClosestCarga($cargas, $lat_mi, $lon_mi);
            echo $cargaMasCercana->id . " " . $cargaMasCercana->id_oferta_detalle . " " . $cargaMasCercana->pesokg, PHP_EOL;
            $idOfertaDetalle =  $cargaMasCercana->id_oferta_detalle;
            $cargasConMismoIdOferta = Utils::getCargasMismaIdOfertaDetalle($cargas, $idOfertaDetalle);
            $cargasQueCumplen = Utils::getCargaSatisfacenAltransporteC($cargasConMismoIdOferta, $pesoMax);


            /**
             * * EXISTE MAS DE UNA SOLA CARGA QUE LLENA TODA LA CAPACIDAD
             */
            if ($cargasQueCumplen->count() > 1) {
                echo "Existe mas de una sola carga que satisface la capacidad por completo o casi por completo", PHP_EOL;
                echo "Cantidad: " . $cargasQueCumplen->count(), PHP_EOL;

                $rutaOferta  = RutaOferta::create([
                    'fecha_recogida' => Carbon::now(),
                    'capacidad_utilizada' => 0,
                    'distancia_total' => 0,
                    'estado' => 'activo'
                ]);
                $sw = true;
                foreach ($cargasQueCumplen as $carga) {
                    if ($sw) {
                        RutaCargaOferta::insert([[
                            'id_carga_oferta' => $carga->id,
                            'id_ruta_oferta' => $rutaOferta->id,
                            'id_transporte' => 1,
                            'orden' => 1,
                            'estado' => 'activo',
                            'distancia' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]]);
                        $rutaOferta->capacidad_utilizada = $carga->pesokg;
                        $sw = false;
                    }
                }
            } elseif ($cargasQueCumplen->count() == 1) {
                echo "Existe una sola carga que satisface la capacidad por completo o casi por completo", PHP_EOL;

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
                        'id_transporte' => 1,
                        'orden' => 1,
                        'estado' => 'activo',
                        'distancia' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]]);
                    $rutaOferta->capacidad_utilizada = $carga->pesokg;
                }
                $rutaOferta->save();
            } elseif ($cargasQueCumplen->count() == 0) {
                echo "No Existe una sola carga que satisface la capacidad por completo o casi por completo", PHP_EOL;
                $sumcargasQueCumplen = Utils::getCargasSatisfacenAltransporte($cargasConMismoIdOferta, $pesoMax);
                $mas10 = $pesoMax + ($pesoMax * 10 / 100);
                $menos10 = $pesoMax - ($pesoMax * 10 / 100);
                $sumaPesoKg = $sumcargasQueCumplen->sum('pesokg');
                if ($sumaPesoKg >= $menos10 && $sumaPesoKg <= $mas10) {
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
                            'id_transporte' => 1,
                            'orden' => 1,
                            'estado' => 'activo',
                            'distancia' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]]);
                        $rutaOferta->capacidad_utilizada += $carga->pesokg;
                    }
                    $rutaOferta->save();
                } else {
                    $peso70 = $pesoMax - ($pesoMax * 40 / 100);
                    $peso50 = $pesoMax - ($pesoMax * 50 / 100);
                    if ($sumaPesoKg >= $peso50 && $sumaPesoKg <= $peso70) {
                    }else {
                        echo "Ni la suma de todas las cargas con la misma IdOferta cumplen", PHP_EOL;
                    }
                }

                /*                 $data = [];
                foreach ($sumcargasQueCumplen as $carga) {
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
            }

            break;
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
    }
}
