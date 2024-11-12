<?php

namespace App\Console\Commands;

use App\Helpers\Utils;
use App\Models\CargaOferta;
use App\Models\Moneda;
use App\Models\OfertaDetalle;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class GenerarCargas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generar:cargas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para generar cargas';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        // Definir rango de fechas
        $fechaInicio = '2024-01-01';
        $fechaFin = '2024-12-31';

        /**
         * $detallesPedidos Obtiene todos los detalles de los pedidos dentro de un rango de fechas
         */
        $detallesPedidos = PedidoDetalle::whereHas('pedido', function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_entrega', [$fechaInicio, $fechaFin])
                ->where('estado_ofertado', 'pendiente');
        })->get();
        //echo "Cantidad de Pedidos: " . $detallesPedidos->count(), PHP_EOL;

        /*         // Preparar los datos para la tabla
        $data = [];
        foreach ($detallesPedidos as $detallePedido) {
            $data[] = [
                $detallePedido->id_pedido,
                $detallePedido->cantidad,
                $detallePedido->producto->nombre,
            ];
        }

        // Crear la salida en consola
        $output = new ConsoleOutput();
        $table = new Table($output);

        // Definir encabezados y filas
        $table
            ->setHeaders(['id_pedido', 'kg_cantidad', 'nombre_producto'])
            ->setRows($data);

        // Renderizar la tabla
        $table->render(); */


        $detallesOfertas = Utils::getDetallesOfertas($fechaInicio, $fechaFin);

        /*         $data = [];
        foreach ($detallesOfertas as $detalleOferta) {
            $data[] = [
                $detalleOferta->id_oferta,
                $detalleOferta->id_produccion,
                $detalleOferta->produccion->descripcion,
                $detalleOferta->precio,
            ];
        }

        // Crear la salida en consola
        $output = new ConsoleOutput();
        $table = new Table($output);

        // Definir encabezados y filas
        $table
            ->setHeaders(['id_oferta', 'id_produccion', 'descripcion_produccion', 'precio'])
            ->setRows($data);

        // Renderizar la tabla
        $table->render(); */


        foreach ($detallesPedidos as $detallePedido) {
            $id_producto = $detallePedido->producto->id;
            $detallesOfertasFiltrados = Utils::getDetallesFiltrados($detallesOfertas, $id_producto);
            if ($detallesOfertasFiltrados->count() == 1) {

                foreach ($detallesOfertasFiltrados as $detalleOferta) {
                    if ($detalleOferta->cantidad_fisico >= $detallePedido->cantidad  and $detalleOferta->cantidad_comprometido + $detallePedido->cantidad <= $detalleOferta->cantidad_fisico) {

                        CargaOferta::insert([[
                            'id_oferta_detalle' => $detalleOferta->id,
                            'pesokg' => $detallePedido->cantidad,
                            'precio' =>  $detallePedido->cantidad * $detalleOferta->preciounitario,
                            'estado' => 'activo',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]]);
                        $detallePedido->update(['cantidad_ofertada' => $detallePedido->cantidad, 'estado_ofertado' => 'ofertado', 'precio_ofertado' => $detallePedido->cantidad * $detalleOferta->preciounitario]);
                        $detalleOferta->update(['cantidad_comprometido' => $detalleOferta->cantidad_comprometido +  $detallePedido->cantidad]);
                    } else {
                        echo "No Se Pudo realizar la carga por que exede la cantiad ofertada", PHP_EOL;
                    }
                }
            } else {
                //echo "Existe mas de 1 ofertaDetalle con el mismo porducto", PHP_EOL;
                $detalleOfertasCumplenCantidad = Utils::getDetalleOfertaQueCumpleConLaCantidad($detallesOfertasFiltrados, $detallePedido->cantidad);

                if ($detalleOfertasCumplenCantidad->count() >= 1) {
                    if ($detalleOfertasCumplenCantidad->count() == 1) {
                        foreach ($detalleOfertasCumplenCantidad as $detalleOferta) {
                            CargaOferta::insert([[
                                'id_oferta_detalle' => $detalleOferta->id,
                                'pesokg' => $detallePedido->cantidad,
                                'precio' =>  $detallePedido->cantidad * $detalleOferta->preciounitario,
                                'estado' => 'activo',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]]);
                            $detallePedido->update(['cantidad_ofertada' => $detallePedido->cantidad, 'estado_ofertado' => 'ofertado', 'precio_ofertado' => $detallePedido->cantidad * $detalleOferta->preciounitario]);
                            $detalleOferta->update(['cantidad_comprometido' => $detalleOferta->cantidad_comprometido +  $detallePedido->cantidad]);
                        }
                    } else {
                        /**
                         * * SE OBTINE EL DETALLE DE LA OFERTA CON EL CRITERIO DE MENOR PRECIO POR UNIDAD YA QUE HAY MAS DE 1 DETALLE DE OFERTAS
                         */
                        //echo "Existe mas de 1 ofertaDetalle con el mismo porducto que cumplen con la cantidad requerida que son:" . $detalleOfertasCumplenCantidad->count(), PHP_EOL;
                        $detalleOfertaConMenorPxU = Utils::getDetalleOfertaMenorPxU($detalleOfertasCumplenCantidad);
                        //echo $detalleOfertaConMenorPxU->id , PHP_EOL;

                        CargaOferta::insert([[
                            'id_oferta_detalle' => $detalleOfertaConMenorPxU->id,
                            'pesokg' => $detallePedido->cantidad,
                            'precio' =>  $detallePedido->cantidad * $detalleOfertaConMenorPxU->preciounitario,
                            'estado' => 'activo',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]]);
                        $detallePedido->update(['cantidad_ofertada' => $detallePedido->cantidad, 'estado_ofertado' => 'ofertado', 'precio_ofertado' => $detallePedido->cantidad * $detalleOfertaConMenorPxU->preciounitario]);
                        $detalleOfertaConMenorPxU->update(['cantidad_comprometido' => $detalleOfertaConMenorPxU->cantidad_comprometido +  $detallePedido->cantidad]);
                    }
                } else {
                    /**
                     * * VERIFICAR SI ENTRE TODOS LO DETALLES DE LAS OFERTAS PUEDEN SATISFACER EL PEDIDO
                     */
                    //echo "Cantidad Requerida: " .  $detallePedido->cantidad, PHP_EOL;
                    //echo $detallesOfertasFiltrados->count() . " Antes ", PHP_EOL;

                    $detallesOfertasSumadosCumplen =  Utils::getDetallesSatisfacenCantidad($detallesOfertasFiltrados, $detallePedido->cantidad);

                    //echo $detallesOfertasSumadosCumplen->count() . " Despues ", PHP_EOL;
                    $cantidadAcumulada = 0;
                    $precio_a_ofertar = 0;
                    $sw = true;
                    foreach ($detallesOfertasSumadosCumplen as $detalleOferta) {
                        $cantidadAcumulada += $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido;
                        if ($cantidadAcumulada < $detallePedido->cantidad and $sw) {
                            CargaOferta::insert([[
                                'id_oferta_detalle' => $detalleOferta->id,
                                'pesokg' => $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido,
                                'precio' =>  $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido  * $detalleOferta->preciounitario,
                                'estado' => 'activo',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]]);
                            $precio_a_ofertar += ($detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido)  * $detalleOferta->preciounitario;
                            $detalleOferta->update(['cantidad_comprometido' => $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido]);
                        } else {
                            if ($cantidadAcumulada == $detallePedido->cantidad) {

                                CargaOferta::insert([[
                                    'id_oferta_detalle' => $detalleOferta->id,
                                    'pesokg' => $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido,
                                    'precio' =>  $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido  * $detalleOferta->preciounitario,
                                    'estado' => 'activo',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]]);
                                $precio_a_ofertar += ($detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido)  * $detalleOferta->preciounitario;
                                $sw = false;
                                #$cantidadAcumulada = $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido;
                                $detalleOferta->update(['cantidad_comprometido' => $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido]);
                            } elseif ($cantidadAcumulada > $detallePedido->cantidad) {
                                $cantidadRestante = $cantidadAcumulada - $detallePedido->cantidad;
                                CargaOferta::insert([[
                                    'id_oferta_detalle' => $detalleOferta->id,
                                    'pesokg' => $cantidadRestante,
                                    'precio' =>  $cantidadRestante  * $detalleOferta->preciounitario,
                                    'estado' => 'activo',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]]);
                                $sw = false;
                                $precio_a_ofertar += $cantidadRestante  * $detalleOferta->preciounitario;
                                $detalleOferta->update(['cantidad_comprometido' => $cantidadRestante]);
                            }
                        }
                        /* $cantidadAcumulada += $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido;
                        if ($cantidadAcumulada > $detallePedido->cantidad) {
                            $precio_a_ofertar += (($detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido) - $cantidadAcumulada) * $detalleOferta->preciounitario;

                            $a = ($detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido) - $cantidadAcumulada;
                            $detalleOferta->update(['cantidad_comprometido' => $a]);
                        } else {
                            $precio_a_ofertar +=  ($detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido) * $detalleOferta->preciounitario;

                            $detalleOferta->update(['cantidad_comprometido' => $detalleOferta->cantidad_fisico - $detalleOferta->cantidad_comprometido]);
                        } */
                    };

                    if ($cantidadAcumulada == $detallePedido->cantidad) {
                        $detallePedido->update([
                            'cantidad_ofertada' => $cantidadAcumulada,
                            'estado_ofertado' => 'ofertado',
                            'precio_ofertado' => $precio_a_ofertar
                        ]);
                    } else if ($cantidadAcumulada < $detallePedido->cantidad) {

                        $detallePedido->update([
                            'cantidad_ofertada' => $cantidadAcumulada,
                            'estado_ofertado' => 'ofertado',
                            'precio_ofertado' => $precio_a_ofertar
                        ]);
                    } else {

                        $detallePedido->update([
                            'cantidad_ofertada' => $detallePedido->cantidad,
                            'estado_ofertado' => 'ofertado',
                            'precio_ofertado' => $precio_a_ofertar
                        ]);
                    }

                    /*  if ($cantidadAcumulada == $detallePedido->cantidad) {
                        $detallePedido->update([
                            'cantidad_ofertada' => $cantidadAcumulada,
                            'estado_ofertado' => 'ofertado',
                            'precio_ofertado' => $precio_a_ofertar
                        ]);
                    } else {
                    } */
                }
            }
        }
        /*         $idProducto = 1; // Cambia este valor al ID del producto que quieres filtrar

        // Filtrar los detalles de las ofertas por el id_producto
        $detallesFiltrados = Utils::getDetallesFiltrados($detallesOfertas, $idProducto);

        $data = [];
        foreach ($detallesFiltrados as $detalleOferta) {
            $data[] = [
                $detalleOferta->id_oferta,
                $detalleOferta->id_produccion,
                $detalleOferta->produccion->descripcion,
                $detalleOferta->precio,
            ];
        }

        // Crear la salida en consola
        $output = new ConsoleOutput();
        $table = new Table($output);

        // Definir encabezados y filas
        $table
            ->setHeaders(['id_oferta', 'id_produccion', 'descripcion_produccion', 'precio'])
            ->setRows($data);

        // Renderizar la tabla
        $table->render(); */

        /**
         * * GENERAR CARGAS 
         */

        /*        if ($detallesFiltrados->count() == 1) {

            foreach ($detallesFiltrados as $detalleOferta) {
                CargaOferta::insert([[
                    'id_oferta_detalle' => $detalleOferta->id,
                    'pesokg' => 400,
                    'precio' => 10,
                    'estado' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]]);
            }
        } */
    }
}
