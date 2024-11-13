<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoDetalle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PedidoDetalleController extends Controller
{
    // Obtener todos los detalles de pedidos con sus relaciones
    public function index()
    {
        return response()->json(PedidoDetalle::with('pedido', 'producto', 'unidadMedida', 'cargas')->get(), 200);
    }

    // Crear un nuevo detalle de pedido
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_pedido' => 'required|exists:pedidos,id',
                'id_producto' => 'required|exists:productos,id',
                'id_unidadmedida' => 'required|exists:unidad_medidas,id',
                'cantidad' => 'required|numeric|min:1',
                'cantidad_ofertada' => 'nullable|numeric|min:0'
            ], [
                'id_pedido.required' => 'El campo id_pedido es obligatorio.',
                'id_pedido.exists' => 'El pedido especificado no existe.',
                'id_producto.required' => 'El campo id_producto es obligatorio.',
                'id_producto.exists' => 'El producto especificado no existe.',
                'id_unidadmedida.required' => 'El campo id_unidadmedida es obligatorio.',
                'id_unidadmedida.exists' => 'La unidad de medida especificada no existe.',
                'cantidad.required' => 'El campo cantidad es obligatorio.',
                'cantidad.numeric' => 'La cantidad debe ser un número.',
                'cantidad.min' => 'La cantidad debe ser al menos 1.',
            ]);

            $detalle = PedidoDetalle::create($request->all());
            $detalle->actualizarEstadoOfertado(); // Actualiza el estado basado en las cantidades
            return response()->json($detalle, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un detalle de pedido específico con sus relaciones
    public function show($id)
    {
        try {
            $pedidoDetalle = PedidoDetalle::with('pedido', 'producto', 'unidadMedida', 'cargas')->findOrFail($id);
            return response()->json($pedidoDetalle, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }
    }

    // Actualizar datos de un detalle de pedido
    public function update(Request $request, $id)
    {
        try {
            $detalle = PedidoDetalle::findOrFail($id);

            $request->validate([
                'id_pedido' => 'sometimes|required|exists:pedidos,id',
                'id_producto' => 'sometimes|required|exists:productos,id',
                'id_unidadmedida' => 'sometimes|required|exists:unidad_medidas,id',
                'cantidad' => 'sometimes|required|numeric|min:1',
                'cantidad_ofertada' => 'nullable|numeric|min:0'
            ], [
                'id_pedido.exists' => 'El pedido especificado no existe.',
                'id_producto.exists' => 'El producto especificado no existe.',
                'id_unidadmedida.exists' => 'La unidad de medida especificada no existe.',
                'cantidad.numeric' => 'La cantidad debe ser un número.',
                'cantidad.min' => 'La cantidad debe ser al menos 1.',
            ]);

            $detalle->update($request->all());
            $detalle->actualizarEstadoOfertado(); // Actualiza el estado basado en las cantidades
            return response()->json($detalle, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }
    }

    // Eliminar un detalle de pedido
    public function destroy($id)
    {
        try {
            $detalle = PedidoDetalle::findOrFail($id);
            $detalle->delete();
            return response()->json(['message' => 'Detalle de pedido eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }
    }

    // Obtener todas las cargas asociadas con un detalle de pedido específico
    public function getCargas($id)
    {
        try {
            $detalle = PedidoDetalle::with('unidadMedida')->findOrFail($id);
            $cargas = $detalle->cargas;

            if ($cargas->isEmpty()) {
                return response()->json(['message' => 'No se encontraron cargas para este detalle de pedido'], 404);
            }

            return response()->json($cargas, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }
    }

    // Obtener todos los detalles de un pedido específico
    public function getDetallesByPedido($pedidoId)
    {
        $detalles = PedidoDetalle::with('pedido', 'producto', 'unidadMedida', 'cargas')->where('id_pedido', $pedidoId)->get();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles para este pedido'], 404);
        }

        return response()->json($detalles, 200);
    }
}
