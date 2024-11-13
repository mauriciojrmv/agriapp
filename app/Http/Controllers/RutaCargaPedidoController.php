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
        $rutasCargasPedidos = RutaCargaPedido::with(['cargaPedido', 'rutaPedido', 'transporte'])->get();
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
            $rutaCargaPedido = RutaCargaPedido::with(['cargaPedido', 'rutaPedido', 'transporte'])->findOrFail($id);
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
}
