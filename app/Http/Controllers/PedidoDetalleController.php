<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\CargaPedido;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PedidoDetalleController extends Controller
{
    // Obtener todos los detalles de pedidos
    public function index()
    {
        return response()->json(PedidoDetalle::all(), 200);
    }

    // Crear un nuevo detalle de pedido
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_pedido' => 'required|exists:pedidos,id',
                'id_producto' => 'required|exists:productos,id',
                'cantidad' => 'required|numeric|min:1',
                'cantidad_ofertada' => 'nullable|numeric|min:0'
            ], [
                'id_pedido.required' => 'El campo id_pedido es obligatorio.',
                'id_pedido.exists' => 'El pedido especificado no existe.',
                'id_producto.required' => 'El campo id_producto es obligatorio.',
                'id_producto.exists' => 'El producto especificado no existe.',
                'cantidad.required' => 'El campo cantidad es obligatorio.',
                'cantidad.numeric' => 'La cantidad debe ser un número.',
                'cantidad.min' => 'La cantidad debe ser al menos 1.',
            ]);

            $detalle = PedidoDetalle::create($request->all());
            return response()->json($detalle, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un detalle de pedido específico
    public function show($id)
    {
        try {
            $detalle = PedidoDetalle::findOrFail($id);
            return response()->json($detalle, 200);
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
                'cantidad' => 'sometimes|required|numeric|min:1',
                'cantidad_ofertada' => 'nullable|numeric|min:0'
            ], [
                'id_pedido.exists' => 'El pedido especificado no existe.',
                'id_producto.exists' => 'El producto especificado no existe.',
                'cantidad.numeric' => 'La cantidad debe ser un número.',
                'cantidad.min' => 'La cantidad debe ser al menos 1.',
            ]);

            $detalle->update($request->all());
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
            $detalle = PedidoDetalle::findOrFail($id);
            $cargas = CargaPedido::where('id_pedido_detalle', $id)->get();

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
        $detalles = PedidoDetalle::where('id_pedido', $pedidoId)->get();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles para este pedido'], 404);
        }

        return response()->json($detalles, 200);
    }

    // Obtener todos los detalles de pedidos que incluyen un producto específico
    public function getDetallesByProducto($productoId)
    {
        $detalles = PedidoDetalle::where('id_producto', $productoId)->get();

        if ($detalles->isEmpty()) {
            return response()->json(['message' => 'No se encontraron detalles de pedido para este producto'], 404);
        }

        return response()->json($detalles, 200);
    }

    // Actualizar cantidad de múltiples detalles de pedido
    public function updateCantidadBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pedido_detalles,id',
            'cantidad' => 'required|numeric|min:1'
        ]);

        $ids = $request->input('ids');
        $cantidad = $request->input('cantidad');

        PedidoDetalle::whereIn('id', $ids)->update(['cantidad' => $cantidad]);

        return response()->json(['message' => 'Cantidad actualizada para los detalles de pedido seleccionados'], 200);
    }

    // Obtener un resumen de la cantidad total de cada producto solicitado en los pedidos
    public function getResumenProductos()
    {
        $resumen = PedidoDetalle::select('id_producto')
            ->selectRaw('SUM(cantidad) as total_cantidad')
            ->groupBy('id_producto')
            ->get();

        return response()->json($resumen, 200);
    }

    // Verificar si la cantidad solicitada es menor o igual a la cantidad ofertada
    public function checkAvailability($id)
    {
        try {
            $detalle = PedidoDetalle::findOrFail($id);
            $disponible = $detalle->cantidad <= $detalle->cantidad_ofertada;

            return response()->json([
                'pedido_detalle_id' => $id,
                'disponible' => $disponible
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Detalle de pedido no encontrado'], 404);
        }
    }
}
