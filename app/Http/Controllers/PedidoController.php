<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller
{
    // Obtener todos los pedidos
    public function index()
    {
        return response()->json(Pedido::all(), 200);
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
                'estado' => 'required|string|max:255',
            ], [
                'id_cliente.required' => 'El campo id_cliente es obligatorio.',
                'id_cliente.exists' => 'El cliente especificado no existe.',
                'fecha_entrega.required' => 'El campo fecha de entrega es obligatorio.',
                'ubicacion_latitud.required' => 'La latitud de ubicación es obligatoria.',
                'ubicacion_longitud.required' => 'La longitud de ubicación es obligatoria.',
                'estado.required' => 'El campo estado es obligatorio.'
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

    // Mostrar detalles de un pedido específico
    public function show($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
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
                'estado' => 'sometimes|required|string|max:255',
            ], [
                'id_cliente.exists' => 'El cliente especificado no existe.',
                'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
                'ubicacion_latitud.numeric' => 'La latitud de ubicación debe ser un número.',
                'ubicacion_longitud.numeric' => 'La longitud de ubicación debe ser un número.',
                'estado.string' => 'El estado debe ser una cadena de texto.'
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
            $detalles = PedidoDetalle::where('id_pedido', $id)->get();

            if ($detalles->isEmpty()) {
                return response()->json(['message' => 'No se encontraron detalles para este pedido'], 404);
            }

            return response()->json($detalles, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }
    }

    // Obtener pedidos por estado
    public function getPedidosByEstado($estado)
    {
        $pedidos = Pedido::where('estado', $estado)->get();

        if ($pedidos->isEmpty()) {
            return response()->json(['message' => "No se encontraron pedidos con el estado: $estado"], 404);
        }

        return response()->json($pedidos, 200);
    }

    // Actualizar estado de múltiples pedidos
    public function updateEstadoBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pedidos,id',
            'estado' => 'required|string|max:255'
        ]);

        $ids = $request->input('ids');
        $estado = $request->input('estado');

        Pedido::whereIn('id', $ids)->update(['estado' => $estado]);

        return response()->json(['message' => 'Estado actualizado para los pedidos seleccionados'], 200);
    }

    // Obtener el historial de pedidos de un cliente
    public function getPedidosByCliente($clienteId)
    {
        $pedidos = Pedido::where('id_cliente', $clienteId)->get();

        if ($pedidos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron pedidos para este cliente'], 404);
        }

        return response()->json($pedidos, 200);
    }

    // Buscar pedidos por rango de fecha
    public function getPedidosByFecha(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $pedidos = Pedido::whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin])->get();

        if ($pedidos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron pedidos en el rango de fechas especificado'], 404);
        }

        return response()->json($pedidos, 200);
    }
}
