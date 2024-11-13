<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    // Obtener todos los productos con sus relaciones
    public function index()
    {
        return response()->json(Producto::with('categoria', 'producciones', 'pedidoDetalles')->get(), 200);
    }

    // Crear un nuevo producto
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_categoria' => 'required|exists:categorias,id',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string'
            ], [
                'id_categoria.required' => 'El campo id_categoria es obligatorio.',
                'id_categoria.exists' => 'La categoría especificada no existe.',
                'nombre.required' => 'El campo nombre es obligatorio.'
            ]);

            $producto = Producto::create($request->all());
            return response()->json($producto, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un producto específico con sus relaciones
    public function show($id)
    {
        try {
            $producto = Producto::with('categoria', 'producciones', 'pedidoDetalles')->findOrFail($id);
            return response()->json($producto, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Actualizar datos de un producto
    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $request->validate([
                'id_categoria' => 'sometimes|required|exists:categorias,id',
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string'
            ], [
                'id_categoria.exists' => 'La categoría especificada no existe.',
                'nombre.required' => 'El campo nombre es obligatorio.'
            ]);

            $producto->update($request->all());
            return response()->json($producto, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Eliminar un producto
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();
            return response()->json(['message' => 'Producto eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Obtener todas las producciones relacionadas con un producto específico
    public function getProducciones($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producciones = $producto->producciones;

            if ($producciones->isEmpty()) {
                return response()->json(['message' => 'No se encontraron producciones para este producto'], 404);
            }

            return response()->json($producciones, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }
}
