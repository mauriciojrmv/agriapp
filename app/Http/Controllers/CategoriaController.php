<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class CategoriaController extends Controller
{
    // Obtener todas las categorías
    public function index()
    {
        return response()->json(Categoria::all(), 200);
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.'
            ]);

            $categoria = Categoria::create($request->all());
            return response()->json($categoria, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una categoría específica
    public function show($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            return response()->json($categoria, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
    }

    // Actualizar datos de una categoría
    public function update(Request $request, $id)
    {
        try {
            $categoria = Categoria::findOrFail($id);

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'nullable|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.'
            ]);

            $categoria->update($request->all());
            return response()->json($categoria, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();
            return response()->json(['message' => 'Categoría eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
    }

    // Obtener todos los productos de una categoría específica
    public function getProductos($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $productos = Producto::where('id_categoria', $id)->get();

            if ($productos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron productos para esta categoría'], 404);
            }

            return response()->json($productos, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
    }
}
