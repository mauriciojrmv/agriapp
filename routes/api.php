<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\AgricultorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TerrenoController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\MonedaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoDetalleController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\OfertaDetalleController;
use App\Http\Controllers\CargaOfertaController;
use App\Http\Controllers\RutaOfertaController;
use App\Http\Controllers\RutaCargaOfertaController;
use App\Http\Controllers\CargaPedidoController;
use App\Http\Controllers\RutaPedidoController;
use App\Http\Controllers\RutaCargaPedidoController;

Route::prefix('v1')->group(function () {
    // Rutas para entidades principales
    Route::apiResource('conductores', ConductorController::class);
    Route::get('conductores/{id}/transportes', [ConductorController::class, 'getTransportes'])->name('conductores.transportesList');
    Route::get('conductores/{id}/puntos-ofertas', [ConductorController::class, 'getPuntosOfertas'])->name('conductores.puntosOfertas');
    Route::get('conductores/{id}/rutas-carga-ofertas', [ConductorController::class, 'getRutasCargaOfertas'])->name('conductores.rutasCargaOfertas');
    Route::get('conductores/{id}/orden-ofertas', [ConductorController::class, 'getOrdenOfertas'])->name('conductores.ordenOfertas');
    Route::get('carga_ofertas/{idCargaOferta}/detalle', [ConductorController::class, 'getDetalleOfertaCarga'])->name('cargaOfertas.detalle');
    Route::get('conductores/{id}/fechas-recogida', [ConductorController::class, 'getFechaRecogida'])->name('conductores.fechasRecogida');
    Route::get('conductores/{id}/carga_ofertas/{idCargaOferta}/detalle', [ConductorController::class, 'getDetalleOfertaCarga'])
    ->name('conductores.cargaOfertas.detalle');
    Route::get('conductores/tipo/{tipo}', [ConductorController::class, 'getConductoresPorTipo'])
    ->name('conductores.porTipo');

    Route::get('conductores/{id}/puntos-pedidos', [ConductorController::class, 'getPuntosPedidos'])->name('conductores.puntosPedidos');
    Route::get('conductores/{id}/rutas-cargas-pedidos', [ConductorController::class, 'getRutasCargasPedidos'])->name('conductores.rutasCargasPedidos');
    Route::get('conductores/{id}/orden-pedidos', [ConductorController::class, 'getOrdenPedidos'])->name('conductores.ordenPedidos');
    Route::get('carga_pedidos/{idCargaPedido}/detalle', [ConductorController::class, 'getDetallePedidoCarga'])->name('cargaPedidos.detalle');
    Route::get('conductores/{id}/fechas-delivery', [ConductorController::class, 'getFechaDelivery'])->name('conductores.fechasDelivery');
    Route::get('conductores/{id}/carga_pedidos/{idCargaPedido}/detalle', [ConductorController::class, 'getDetallePedidoCarga'])
    ->name('conductores.cargaPedidos.detalle');



    Route::apiResource('agricultors', AgricultorController::class);
    Route::get('agricultors/{id}/terrenos', [AgricultorController::class, 'getTerrenosByAgricultorId'])->name('agricultors.terrenosByAgricultor');
    Route::get('agricultors/{id}/producciones', [AgricultorController::class, 'getProduccionesByAgricultorId'])->name('agricultors.produccionesList');
    Route::get('agricultors/{id}/ofertas', [AgricultorController::class, 'getOfertasByAgricultorId'])->name('agricultors.ofertas');
    Route::get('agricultors/{id}/oferta_detalles', [AgricultorController::class, 'getOfertaDetallesByAgricultorId'])->name('agricultors.ofertaDetalles');
    Route::get('agricultors/{id}/oferta_cargas', [AgricultorController::class, 'getOfertaCargasByAgricultorId'])->name('agricultors.ofertaCargas');

    Route::apiResource('clientes', ClienteController::class);
    Route::get('clientes/{id}/pedidos', [ClienteController::class, 'getPedidos'])->name('clientes.pedidosList');

    Route::apiResource('temporadas', TemporadaController::class);
    Route::get('temporadas/{id}/producciones', [TemporadaController::class, 'getProducciones'])->name('temporadas.produccionesList');

    Route::apiResource('categorias', CategoriaController::class);
    Route::get('categorias/{id}/productos', [CategoriaController::class, 'getProductos'])->name('categorias.productosList');

    Route::apiResource('transportes', TransporteController::class);
    Route::get('conductores/{conductorId}/transportes', [TransporteController::class, 'getTransportesByConductor'])->name('conductores.transportesByConductor');
    Route::get('transportes/buscar', [TransporteController::class, 'searchByCapacity'])->name('transportes.buscar');

    Route::apiResource('productos', ProductoController::class);
    Route::get('productos/{id}/producciones', [ProductoController::class, 'getProducciones'])->name('productos.produccionesList');

    Route::apiResource('terrenos', TerrenoController::class);
    Route::get('terrenos/{id}/producciones', [TerrenoController::class, 'getProducciones'])->name('terrenos.produccionesList');

    Route::apiResource('unidad_medidas', UnidadMedidaController::class);
    Route::apiResource('monedas', MonedaController::class);

    // Rutas para pedidos y detalles de pedidos
    Route::apiResource('pedidos', PedidoController::class);
    Route::get('pedidos/{id}/detalles', [PedidoController::class, 'getDetalles'])->name('pedidos.detallesList');
    Route::get('pedidos/estado/{estado}', [PedidoController::class, 'getPedidosByEstado'])->name('pedidos.estadoList');
    Route::put('pedidos/estado/batch', [PedidoController::class, 'updateEstadoBatch'])->name('pedidos.estado.batch');

    Route::apiResource('pedido_detalles', PedidoDetalleController::class);
    Route::get('pedido_detalles/{id}/cargas', [PedidoDetalleController::class, 'getCargasPedidos'])->name('pedido_detalles.cargasList');

    // Rutas para producciones, ofertas y detalles de ofertas
    Route::apiResource('producciones', ProduccionController::class);
    Route::apiResource('ofertas', OfertaController::class);
    Route::apiResource('oferta_detalles', OfertaDetalleController::class);

    // Rutas para cargas y rutas de ofertas
    Route::apiResource('carga_ofertas', CargaOfertaController::class);
    Route::apiResource('ruta_ofertas', RutaOfertaController::class);
    Route::apiResource('ruta_carga_ofertas', RutaCargaOfertaController::class);
    Route::get('ruta_ofertas/{id}/cargas', [RutaOfertaController::class, 'getCargasOfertas'])
    ->name('ruta_ofertas.getCargas');
    Route::get('ruta_ofertas/{id}/puntos-ruta', [RutaCargaOfertaController::class, 'getPuntosRuta'])
    ->name('ruta_ofertas.puntosRuta');
    Route::put('ruta_ofertas/{id}/terminar', [RutaOfertaController::class, 'terminarRuta'])
    ->name('ruta_ofertas.terminarRuta');



    // Rutas personalizadas para RutaCargaOferta
    Route::put('ruta_carga_ofertas/{id}/estado-conductor', [RutaCargaOfertaController::class, 'updateEstadoConductor'])
        ->name('ruta_carga_ofertas.updateEstadoConductor'); // Actualizar estado del conductor
    Route::put('ruta_carga_ofertas/{id}/confirmar-recogida', [RutaCargaOfertaController::class, 'confirmarRecogida'])
        ->name('ruta_carga_ofertas.confirmarRecogida'); // Confirmar recogida de carga
    Route::put('ruta_carga_ofertas/{id}/aceptar', [RutaCargaOfertaController::class, 'aceptarRuta'])
    ->name('ruta_carga_ofertas.aceptarRuta');
    Route::put('ruta_carga_ofertas/{id}/terminar', [RutaCargaOfertaController::class, 'terminarRuta'])
    ->name('ruta_carga_ofertas.terminarRuta');



// Rutas para cargas y rutas de pedidos
Route::apiResource('carga_pedidos', CargaPedidoController::class);
Route::apiResource('ruta_pedidos', RutaPedidoController::class);
Route::apiResource('ruta_carga_pedidos', RutaCargaPedidoController::class);
});
Route::get('ruta_pedidos/{id}/cargas', [RutaPedidoController::class, 'getCargas'])
    ->name('ruta_pedidos.getCargas');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
