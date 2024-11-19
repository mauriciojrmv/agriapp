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

    Route::apiResource('agricultors', AgricultorController::class);
    Route::get('agricultors/{id}/terrenos', [AgricultorController::class, 'getTerrenos'])->name('agricultors.terrenosList');
    Route::get('agricultors/{id}/producciones', [AgricultorController::class, 'getProducciones'])->name('agricultors.produccionesList');

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
    Route::get('clientes/{clienteId}/pedidos', [PedidoController::class, 'getPedidosByCliente'])->name('clientes.pedidosByCliente');
    Route::get('pedidos/fecha', [PedidoController::class, 'getPedidosByFecha'])->name('pedidos.fechaList');

    Route::apiResource('pedido_detalles', PedidoDetalleController::class);
    Route::get('pedido_detalles/{id}/cargas', [PedidoDetalleController::class, 'getCargas'])->name('pedido_detalles.cargasList');
    Route::get('pedidos/{pedidoId}/detalles', [PedidoDetalleController::class, 'getDetallesByPedido'])->name('pedidos.detallesByPedido');
    Route::get('productos/{productoId}/detalles', [PedidoDetalleController::class, 'getDetallesByProducto'])->name('productos.detallesByProducto');
    Route::put('pedido_detalles/cantidad/batch', [PedidoDetalleController::class, 'updateCantidadBatch'])->name('pedido_detalles.cantidad.batch');
    Route::get('pedido_detalles/resumen/productos', [PedidoDetalleController::class, 'getResumenProductos'])->name('pedido_detalles.resumenProductos');
    Route::get('pedido_detalles/{id}/disponibilidad', [PedidoDetalleController::class, 'checkAvailability'])->name('pedido_detalles.disponibilidadCheck');

    // Rutas para producciones ofertas y detalles de ofertas
    Route::apiResource('producciones', ProduccionController::class);
    Route::get('producciones/activas', [ProduccionController::class, 'getProduccionesActivas'])->name('producciones.activasList');
    Route::get('terrenos/{terrenoId}/producciones', [ProduccionController::class, 'getProduccionesByTerreno'])->name('terrenos.produccionesByTerreno');
    Route::get('temporadas/{temporadaId}/producciones', [ProduccionController::class, 'getProduccionesByTemporada'])->name('temporadas.produccionesByTemporada');
    Route::get('productos/{productoId}/producciones', [ProduccionController::class, 'getProduccionesByProducto'])->name('productos.produccionesByProducto');

    Route::apiResource('ofertas', OfertaController::class);
    Route::get('ofertas/{id}/detalles', [OfertaController::class, 'getDetalles'])->name('ofertas.detallesList');
    Route::get('ofertas/activas', [OfertaController::class, 'getOfertasActivas'])->name('ofertas.activasList');
    Route::get('produccions/{produccionId}/ofertas', [OfertaController::class, 'getOfertasByProduccion'])->name('produccions.ofertasList');
    Route::put('ofertas/{id}/extender', [OfertaController::class, 'extendExpiracion'])->name('ofertas.extenderExpiracion');

    Route::apiResource('oferta_detalles', OfertaDetalleController::class);
    Route::get('oferta_detalles/{id}/cargas', [OfertaDetalleController::class, 'getCargas'])->name('oferta_detalles.cargasList');
    Route::get('oferta_detalles/{id}/disponibilidad', [OfertaDetalleController::class, 'checkDisponibilidad'])->name('oferta_detalles.disponibilidadCheck');
    Route::get('monedas/{monedaId}/oferta_detalles', [OfertaDetalleController::class, 'getDetallesByMoneda'])->name('monedas.ofertaDetallesList');
    Route::get('unidad_medidas/{unidadMedidaId}/oferta_detalles', [OfertaDetalleController::class, 'getDetallesByUnidadMedida'])->name('unidad_medidas.ofertaDetallesList');

    // Rutas para cargas y rutas de ofertas
    Route::apiResource('carga_ofertas', CargaOfertaController::class);
    Route::get('carga_ofertas/detalle/{ofertaDetalleId}', [CargaOfertaController::class, 'getCargasByDetalle'])->name('carga_ofertas.detalleList');
    Route::get('carga_ofertas/estado/{estado}', [CargaOfertaController::class, 'getCargasByEstado'])->name('carga_ofertas.estadoList');

    Route::apiResource('ruta_ofertas', RutaOfertaController::class);
    Route::get('ruta_ofertas/{id}/cargas', [RutaOfertaController::class, 'getCargas'])->name('ruta_ofertas.cargasList');

    Route::apiResource('ruta_carga_ofertas', RutaCargaOfertaController::class);

    // Rutas para cargas y rutas de pedidos
    Route::apiResource('carga_pedidos', CargaPedidoController::class);
    Route::apiResource('ruta_pedidos', RutaPedidoController::class);
    Route::apiResource('ruta_carga_pedidos', RutaCargaPedidoController::class);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
