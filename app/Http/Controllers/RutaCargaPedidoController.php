<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RutaCargaPedido;
use App\Models\CargaPedido;
use App\Models\RutaPedido;
use App\Models\Transporte;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RutaCargaPedidoController extends Controller
{
    // Obtener todas las cargas asociadas a rutas de pedido
    public function index()
    {
        $rutasCargasPedidos = RutaCargaPedido::with([
            'cargaPedido.pedidoDetalle.producto',
            'cargaPedido.pedidoDetalle.unidadMedida',
            'cargaPedido.pedidoDetalle.pedido',
            'rutaPedido',
            'transporte.conductor'
            ])->get();
        return response()->json($rutasCargasPedidos, 200);
    }

    // Crear una nueva carga de ruta de pedido
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_carga_pedido' => 'required|exists:carga_pedidos,id',
                'id_ruta_pedido' => 'required|exists:ruta_pedidos,id',
                'id_transporte' => 'required|exists:transportes,id',
                'orden' => 'required|integer|min:1',
                'estado' => 'required|string|max:255',
                'distancia' => 'required|numeric|min:0'
            ], [
                'id_carga_pedido.required' => 'El campo id_carga_pedido es obligatorio.',
                'id_carga_pedido.exists' => 'La carga de pedido especificada no existe.',
                'id_ruta_pedido.required' => 'El campo id_ruta_pedido es obligatorio.',
                'id_ruta_pedido.exists' => 'La ruta de pedido especificada no existe.',
                'id_transporte.required' => 'El campo id_transporte es obligatorio.',
                'id_transporte.exists' => 'El transporte especificado no existe.',
                'orden.required' => 'El campo orden es obligatorio.',
                'orden.integer' => 'El campo orden debe ser un número entero.',
                'estado.required' => 'El campo estado es obligatorio.',
                'distancia.required' => 'El campo distancia es obligatorio.',
                'distancia.numeric' => 'La distancia debe ser un número.'
            ]);

            $rutaCargaPedido = RutaCargaPedido::create($request->all());
            return response()->json($rutaCargaPedido, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una carga de ruta de pedido específica
    public function show($id)
    {
        try {
            $rutaCargaPedido = RutaCargaPedido::with([
            'cargaPedido.pedidoDetalle.producto',
            'cargaPedido.pedidoDetalle.unidadMedida',
            'cargaPedido.pedidoDetalle.pedido',
            'rutaPedido',
            'transporte.conductor'
            ])->findOrFail($id);
            return response()->json($rutaCargaPedido, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de pedido no encontrada'], 404);
        }
    }

    // Actualizar datos de una carga de ruta de pedido
    public function update(Request $request, $id)
    {
        try {
            $rutaCargaPedido = RutaCargaPedido::findOrFail($id);

            $request->validate([
                'id_carga_pedido' => 'sometimes|required|exists:carga_pedidos,id',
                'id_ruta_pedido' => 'sometimes|required|exists:ruta_pedidos,id',
                'id_transporte' => 'sometimes|required|exists:transportes,id',
                'orden' => 'sometimes|required|integer|min:1',
                'estado' => 'sometimes|required|string|max:255',
                'distancia' => 'sometimes|required|numeric|min:0'
            ], [
                'id_carga_pedido.exists' => 'La carga de pedido especificada no existe.',
                'id_ruta_pedido.exists' => 'La ruta de pedido especificada no existe.',
                'id_transporte.exists' => 'El transporte especificado no existe.',
                'orden.integer' => 'El campo orden debe ser un número entero.',
                'distancia.numeric' => 'La distancia debe ser un número.'
            ]);

            $rutaCargaPedido->update($request->all());
            return response()->json($rutaCargaPedido, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de pedido no encontrada'], 404);
        }
    }

    // Eliminar una carga de ruta de pedido
    public function destroy($id)
    {
        try {
            $rutaCargaPedido = RutaCargaPedido::findOrFail($id);
            $rutaCargaPedido->delete();
            return response()->json(['message' => 'Carga de ruta de pedido eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de ruta de pedido no encontrada'], 404);
        }
    }

    public function confirmarPedido(Request $request, $id)
    {
        try {
            $rutaCargaPedido = RutaCargaPedido::findOrFail($id);

            // Validar que la ruta esté en estado en_proceso
            if ($rutaCargaPedido->estado !== 'en_proceso') {
                return response()->json([
                    'message' => 'La carga no está en estado de proceso para ser confirmada.'
                ], 422);
            }

            // Obtener la carga asociada
            $cargaPedido = CargaPedido::with('pedido')->findOrFail($rutaCargaPedido->id_carga_pedido);

            // Actualizar el estado de la carga a finalizado
            $cargaPedido->update(['estado' => 'finalizado']);
            $rutaCargaPedido->update(['estado' => 'finalizado']);

            // Verificar si todas las cargas asociadas a la ruta ya están finalizadas
            $rutaPedido = $rutaCargaPedido->rutaPedido;
            $cargasPendientesRuta = $rutaPedido->rutaCargasPedidos()->where('estado', '!=', 'finalizado')->count();

            if ($cargasPendientesRuta === 0) {
                // Todas las cargas en esta ruta están finalizadas; finalizar la ruta
                $rutaPedido->update(['estado' => 'finalizado']);
            } else {
                // Mantener la ruta en estado en_proceso mientras haya cargas pendientes
                $rutaPedido->update(['estado' => 'en_proceso']);
            }

            // Verificar si todas las cargas del pedido están finalizadas
            $pedido = $cargaPedido->pedido;
            $cargasPedidoPendientes = $pedido->pedidoDetalles()->whereHas('cargaPedidos', function ($query) {
                $query->where('estado', '!=', 'finalizado');
            })->count();

            if ($cargasPedidoPendientes === 0) {
                // Todas las cargas asociadas al pedido están finalizadas; finalizar el pedido
                $pedido->update(['estado' => 'finalizado']);
            }

            return response()->json([
                'message' => 'Entrega del pedido confirmada exitosamente.',
                'rutaCargaPedido' => $rutaCargaPedido,
                'cargaPedido' => $cargaPedido,
                'rutaPedido' => $rutaPedido,
                'pedido' => $pedido,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'RutaCargaPedido, CargaPedido o Pedido no encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al confirmar la entrega.', 'error' => $e->getMessage()], 500);
        }
    }



public function getPuntosRuta($idRutaPedido)
{
    try {
        // Recuperar la RutaPedido con sus RutaCargaPedido relacionadas
        $rutaPedido = RutaPedido::with(['rutaCargaPedido.cargaPedido.pedidoDetalle.producto', 'rutaCargaPedido.cargaPedido.pedido.cliente'])
            ->findOrFail($idRutaPedido);

        // Obtener los puntos de entrega con información adicional
        $puntos = $rutaPedido->rutaCargaPedido->map(function ($rutaCarga) {
            $carga = $rutaCarga->cargaPedido;
            $pedidoDetalle = $carga->pedidoDetalle;
            $producto = $pedidoDetalle->producto;
            $pedido = $carga->pedido;

            return [
                'lat' => $pedido->ubicacion_latitud, // Latitud del pedido (entrega)
                'lon' => $pedido->ubicacion_longitud, // Longitud del pedido (entrega)
                'tipo' => 'delivery',
                'id_carga_pedido' => $carga->id,
                'producto' => $producto->nombre,
                'cantidad' => $pedidoDetalle->cantidad,
                'unidad' => $producto->unidadMedida->nombre, // Asumiendo relación con UnidadMedida
                'precio' => $pedidoDetalle->precio, // Precio del producto en el pedido
            ];
        });

        // Agregar el punto de origen (punto de acopio)
        $puntos->prepend([
            'lat' => -17.750000, // Latitud del punto de acopio
            'lon' => -63.100000, // Longitud del punto de acopio
            'tipo' => 'punto_acopio',
        ]);

        return response()->json(['puntos_ruta' => $puntos], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'RutaPedido no encontrada.'], 404);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al recuperar los puntos de la ruta.', 'error' => $e->getMessage()], 500);
    }
}



}
