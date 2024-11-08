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
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\OfertaDetalleController;
use App\Http\Controllers\CargaOfertaController;
use App\Http\Controllers\RutaOfertaController;
use App\Http\Controllers\RutaCargaOfertaController;
use App\Http\Controllers\CargaPedidoController;
use App\Http\Controllers\RutaPedidoController;
use App\Http\Controllers\RutaCargaPedidoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
    Route::apiResource('conductores', ConductorController::class);
    Route::apiResource('agricultors', AgricultorController::class);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('temporadas', TemporadaController::class);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('transportes', TransporteController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('terrenos', TerrenoController::class);
    Route::apiResource('unidad_medidas', UnidadMedidaController::class);
    Route::apiResource('monedas', MonedaController::class);

    // Rutas para pedidos y detalles de pedidos
    Route::apiResource('pedidos', PedidoController::class);
    Route::apiResource('pedido_detalles', PedidoDetalleController::class);

    // Rutas para ofertas y detalles de ofertas
    Route::apiResource('ofertas', OfertaController::class);
    Route::apiResource('oferta_detalles', OfertaDetalleController::class);

    // Rutas para cargas y rutas de ofertas
    Route::apiResource('carga_ofertas', CargaOfertaController::class);
    Route::apiResource('ruta_ofertas', RutaOfertaController::class);
    Route::apiResource('ruta_carga_ofertas', RutaCargaOfertaController::class);

    // Rutas para cargas y rutas de pedidos
    Route::apiResource('carga_pedidos', CargaPedidoController::class);
    Route::apiResource('ruta_pedidos', RutaPedidoController::class);
    Route::apiResource('ruta_carga_pedidos', RutaCargaPedidoController::class);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
