<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller
{
    // Obtener todos los pedidos con sus relaciones
    public function index()
    {
        return response()->json(Pedido::with('cliente', 'detalles.producto')->get(), 200);
    }

    // Crear un nuevo pedido
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_cliente' => 'required|exists:clientes,id',
                'fecha_entrega' => 'required|date',
                'ubicacion_latitud' => 'required|numeric',
                'ubicacion_longitud' => 'required|numeric',
                'estado' => 'sometimes|required|string|in:pendiente,completo,incompleto',
            ], [
                'id_cliente.required' => 'El campo id_cliente es obligatorio.',
                'id_cliente.exists' => 'El cliente especificado no existe.',
                'fecha_entrega.required' => 'El campo fecha de entrega es obligatorio.',
                'ubicacion_latitud.required' => 'La latitud de ubicación es obligatoria.',
                'ubicacion_longitud.required' => 'La longitud de ubicación es obligatoria.',
                'estado.in' => 'El campo estado solo puede ser pendiente, completo, incompleto.'
            ]);

            $pedido = Pedido::create($request->all());
            return response()->json($pedido, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un pedido específico con sus relaciones
    public function show($id)
    {
        try {
            $pedido = Pedido::with('cliente', 'detalles.producto')->findOrFail($id);
            return response()->json($pedido, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }
    }

    // Actualizar datos de un pedido
    public function update(Request $request, $id)
    {
        try {
            $pedido = Pedido::findOrFail($id);

            $request->validate([
                'id_cliente' => 'sometimes|required|exists:clientes,id',
                'fecha_entrega' => 'sometimes|required|date',
                'ubicacion_latitud' => 'sometimes|required|numeric',
                'ubicacion_longitud' => 'sometimes|required|numeric',
                'estado' => 'sometimes|required|string|in:pendiente,completo,incompleto',
            ], [
                'id_cliente.exists' => 'El cliente especificado no existe.',
                'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
                'ubicacion_latitud.numeric' => 'La latitud de ubicación debe ser un número.',
                'ubicacion_longitud.numeric' => 'La longitud de ubicación debe ser un número.',
                'estado.string' => 'El estado estado solo puede ser pendiente, completo, incompleto.'
            ]);

            $pedido->update($request->all());
            return response()->json($pedido, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }
    }

    // Eliminar un pedido
    public function destroy($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            $pedido->delete();
            return response()->json(['message' => 'Pedido eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }
    }

    // Obtener todos los detalles de un pedido específico
    public function getDetalles($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            $detalles = $pedido->detalles()->with('producto')->get();

            if ($detalles->isEmpty()) {
                return response()->json(['message' => 'No se encontraron detalles para este pedido'], 404);
            }

            return response()->json($detalles, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }
    }
}
