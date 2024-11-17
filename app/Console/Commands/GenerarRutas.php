<?php

namespace App\Console\Commands;

use App\Models\CargaOferta;
use Illuminate\Console\Command;
use App\Helpers\Utils;
use App\Models\RutaCargaOferta;
use App\Models\RutaOferta;
use Carbon\Carbon;
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
        // Preparar los datos para la tabla
        $lat_mi = -17.845345315463035;
        $lon_mi = -63.181173159935774;
        $pesoMax = 4; // EN KG

        // Encontrar la carga más cercana
        $cargaMasCercana = Utils::findClosestCarga($cargas, $lat_mi, $lon_mi);


        // Mostrar la información de la carga más cercana
        if ($cargaMasCercana) {
            $idOfertaDetalle =  $cargaMasCercana->id_oferta_detalle;
            $cargasConMismoIdOferta = Utils::getCargasMismaIdOfertaDetalle($cargas, $idOfertaDetalle);
            $cargasQueCumplen = Utils::getCargasSatisfacenAltransporte($cargasConMismoIdOferta, $pesoMax);
            //SI SON MAS DE 1 CARGA QUE ENTRARAN EN EL TRANSPORTE
            if ($cargasQueCumplen->count() > 1) {
               


            } elseif ($cargasQueCumplen->count() == 1) {
                $rutaOferta  = RutaOferta::create([
                    'fecha_recogida'=> Carbon::now(), 
                    'capacidad_utilizada'=> 0, 
                    'distancia_total'=> 0, 
                    'estado' => 'activo'
                ]);

                foreach( $cargasQueCumplen as $carga){
                    RutaCargaOferta::insert([[
                        'id_carga_oferta'=>$carga->id, 
                        'id_ruta_oferta'=>$rutaOferta->id, 
                        'id_transporte'=>1, 
                        'orden'=> 1, 
                        'estado'=>'activo', 
                        'distancia'=>0
                    ]]);
                    $rutaOferta->capacidad_utilizada =$carga->pesokg;
                }

                $rutaOferta->save();
            }
        } else {
            echo "No se encontró ninguna carga cercana.\n";
        }


        // $primeraCargaMasCercana = CargaOferta::find

    }
}

//-17.824576881974707, -63.185172946436
