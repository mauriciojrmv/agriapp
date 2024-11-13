<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    // Obtener todos los clientes
    public function index()
    {
        return response()->json(Cliente::with('pedidos')->get(), 200);
    }

    // Crear un nuevo cliente
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'telefono' => 'required|string|max:20|unique:clientes,telefono',
                'email' => 'required|email|unique:clientes,email',
                'direccion' => 'required|string|max:255',
                'password' => 'required|string|min:8'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.unique' => 'Este número de teléfono ya está registrado.',
                'email.required' => 'El campo email es obligatorio.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.required' => 'El campo contraseña es obligatorio.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
            ]);

            $cliente = Cliente::create($request->all());
            return response()->json($cliente, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un cliente específico
    public function show($id)
    {
        try {
            $cliente = Cliente::with('pedidos')->findOrFail($id);
            return response()->json($cliente, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    }

    // Actualizar datos de un cliente
    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'telefono' => 'sometimes|required|string|max:20|unique:clientes,telefono,' . $id,
                'email' => 'sometimes|required|email|unique:clientes,email,' . $id,
                'direccion' => 'sometimes|required|string|max:255',
                'password' => 'sometimes|required|string|min:8'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'apellido.required' => 'El campo apellido es obligatorio.',
                'telefono.unique' => 'Este número de teléfono ya está registrado.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
            ]);

            $cliente->update($request->all());
            return response()->json($cliente, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    }

    // Eliminar un cliente
    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();
            return response()->json(['message' => 'Cliente eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    }

    // Obtener los pedidos de un cliente específico
    public function getPedidos($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $pedidos = Pedido::where('id_cliente', $id)->get();

            if ($pedidos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron pedidos para este cliente'], 404);
            }

            return response()->json($pedidos, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
    }
}
