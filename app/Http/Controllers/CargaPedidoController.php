<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CargaPedido;
use App\Models\PedidoDetalle;
use App\Models\RutaCargaPedido;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CargaPedidoController extends Controller
{
    // Obtener todas las cargas de pedidos
    public function index()
    {
        return response()->json(CargaPedido::with(
        'pedidoDetalle.pedido',
        'pedidoDetalle.producto',
        'pedidoDetalle.unidadMedida',
        'rutaCargaPedido',
        )->get(), 200);
    }

    // Crear una nueva carga de pedido
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_pedido_detalle' => 'required|exists:pedido_detalles,id',
                'cantidad' => 'required|numeric|min:0.1',
                'estado' => 'required|string|max:255'
            ], [
                'id_pedido_detalle.required' => 'El campo id_pedido_detalle es obligatorio.',
                'id_pedido_detalle.exists' => 'El detalle de pedido especificado no existe.',
                'cantidad.required' => 'El campo cantidad es obligatorio.',
                'cantidad.numeric' => 'La cantidad debe ser un número.',
                'estado.required' => 'El campo estado es obligatorio.'
            ]);

            // Validar que la cantidad no exceda la cantidad disponible en el detalle del pedido
            $pedidoDetalle = PedidoDetalle::findOrFail($request->id_pedido_detalle);
            $cantidadDisponible = $pedidoDetalle->cantidad - $pedidoDetalle->cantidad_ofertada;

            if ($request->cantidad > $cantidadDisponible) {
                return response()->json([
                    'message' => 'La cantidad de la carga supera la cantidad disponible en el pedido.'
                ], 422);
            }

            // Actualizar la cantidad ofertada en el detalle del pedido
            $pedidoDetalle->cantidad_ofertada += $request->cantidad;
            $pedidoDetalle->save();

            // Crear la nueva carga de pedido
            $cargaPedido = CargaPedido::create([
                'id_pedido_detalle' => $request->id_pedido_detalle,
                'cantidad' => $request->cantidad,
                'estado' => $request->estado
            ]);

            return response()->json($cargaPedido, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una carga de pedido específica
    public function show($id)
    {
        try {
            $cargaPedido = CargaPedido::with(
        'pedidoDetalle.pedido',
        'pedidoDetalle.producto',
        'pedidoDetalle.unidadMedida',
        'rutaCargaPedido',)->findOrFail($id);
            return response()->json($cargaPedido, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de pedido no encontrada'], 404);
        }
    }

    // Actualizar datos de una carga de pedido
    public function update(Request $request, $id)
    {
        try {
            $cargaPedido = CargaPedido::findOrFail($id);

            $request->validate([
                'id_pedido_detalle' => 'sometimes|required|exists:pedido_detalles,id',
                'cantidad' => 'sometimes|required|numeric|min:0.1',
                'estado' => 'sometimes|required|string|max:255'
            ]);

            // Validar cantidad actualizada si se cambia la cantidad
            if ($request->has('cantidad')) {
                $pedidoDetalle = PedidoDetalle::findOrFail($request->id_pedido_detalle ?? $cargaPedido->id_pedido_detalle);
                $cantidadDisponible = $pedidoDetalle->cantidad - $pedidoDetalle->cantidad_ofertada + $cargaPedido->cantidad;

                if ($request->cantidad > $cantidadDisponible) {
                    return response()->json([
                        'message' => 'La cantidad actualizada de la carga supera la cantidad disponible en el pedido.'
                    ], 422);
                }

                // Actualizar la cantidad ofertada en el detalle del pedido
                $pedidoDetalle->cantidad_ofertada += $request->cantidad - $cargaPedido->cantidad;
                $pedidoDetalle->save();
            }

            $cargaPedido->update($request->all());
            return response()->json($cargaPedido, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de pedido no encontrada'], 404);
        }
    }

    // Eliminar una carga de pedido
    public function destroy($id)
    {
        try {
            $cargaPedido = CargaPedido::findOrFail($id);
            $pedidoDetalle = PedidoDetalle::findOrFail($cargaPedido->id_pedido_detalle);

            // Restar la cantidad de la carga eliminada del detalle del pedido
            $pedidoDetalle->cantidad_ofertada -= $cargaPedido->cantidad;
            $pedidoDetalle->save();

            $cargaPedido->delete();
            return response()->json(['message' => 'Carga de pedido eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Carga de pedido no encontrada'], 404);
        }
    }
}
