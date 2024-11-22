<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RutaPedido;
use App\Models\RutaCargaPedido;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RutaPedidoController extends Controller
{
    // Obtener todas las rutas de pedidos
    public function index()
    {
        return response()->json(RutaPedido::with('rutaCargaPedido')->get(), 200);
    }

    // Crear una nueva ruta de pedido
    public function store(Request $request)
    {
        try {
            $request->validate([
                'fecha_entrega' => 'required|date',
                'capacidad_utilizada' => 'required|numeric|min:0',
                'distancia_total' => 'required|numeric|min:0',
                'estado' => 'required|string|max:255'
            ], [
                'fecha_entrega.required' => 'El campo fecha de entrega es obligatorio.',
                'capacidad_utilizada.required' => 'El campo capacidad utilizada es obligatorio.',
                'distancia_total.required' => 'El campo distancia total es obligatorio.',
                'estado.required' => 'El campo estado es obligatorio.'
            ]);

            $rutaPedido = RutaPedido::create($request->all());
            return response()->json($rutaPedido, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una ruta de pedido específica
    public function show($id)
    {
        try {
            $rutaPedido = RutaPedido::with('rutaCargaPedido')->findOrFail($id);
            return response()->json($rutaPedido, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ruta de pedido no encontrada'], 404);
        }
    }

    // Actualizar datos de una ruta de pedido
    public function update(Request $request, $id)
    {
        try {
            $rutaPedido = RutaPedido::findOrFail($id);

            $request->validate([
                'fecha_entrega' => 'sometimes|required|date',
                'capacidad_utilizada' => 'sometimes|required|numeric|min:0',
                'distancia_total' => 'sometimes|required|numeric|min:0',
                'estado' => 'sometimes|required|string|max:255'
            ]);

            $rutaPedido->update($request->all());
            return response()->json($rutaPedido, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ruta de pedido no encontrada'], 404);
        }
    }

    // Eliminar una ruta de pedido
    public function destroy($id)
    {
        try {
            $rutaPedido = RutaPedido::findOrFail($id);

            // Eliminar todas las cargas asociadas a esta ruta de pedido
            RutaCargaPedido::where('id_ruta_pedido', $id)->delete();

            $rutaPedido->delete();
            return response()->json(['message' => 'Ruta de pedido eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ruta de pedido no encontrada'], 404);
        }
    }

    // Obtener todas las cargas asociadas con una ruta de pedido específica con detalles relevantes
public function getCargasPedidos($id)
{
    try {
        // Verificar si la RutaPedido existe
        $rutaPedido = RutaPedido::findOrFail($id);

        // Obtener las cargas asociadas a la RutaPedido
        $cargas = RutaCargaPedido::where('id_ruta_pedido', $id)
            ->with(['cargaPedido.pedidoDetalle.producto'])
            ->get();

        // Validar si se encontraron cargas asociadas
        if ($cargas->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron cargas para esta ruta de pedido'
            ], 404);
        }

        // Retornar la respuesta con las cargas
        return response()->json([
            'status' => 'success',
            'data' => $cargas
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Ruta de pedido no encontrada'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Ocurrió un error al obtener las cargas',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
