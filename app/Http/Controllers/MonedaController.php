<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Moneda;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class MonedaController extends Controller
{
    // Obtener todas las monedas
    public function index()
    {
        return response()->json(Moneda::all(), 200);
    }

    // Crear una nueva moneda
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255|unique:monedas,nombre',
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'nombre.unique' => 'El nombre de la moneda ya existe.'
            ]);

            $moneda = Moneda::create($request->all());
            return response()->json($moneda, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una moneda específica
    public function show($id)
    {
        try {
            $moneda = Moneda::findOrFail($id);
            return response()->json($moneda, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Moneda no encontrada'], 404);
        }
    }

    // Actualizar datos de una moneda
    public function update(Request $request, $id)
    {
        try {
            $moneda = Moneda::findOrFail($id);

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255|unique:monedas,nombre,' . $id,
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'nombre.unique' => 'El nombre de la moneda ya existe.'
            ]);

            $moneda->update($request->all());
            return response()->json($moneda, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Moneda no encontrada'], 404);
        }
    }

    // Eliminar una moneda
    public function destroy($id)
    {
        try {
            $moneda = Moneda::findOrFail($id);
            $moneda->delete();
            return response()->json(['message' => 'Moneda eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Moneda no encontrada'], 404);
        }
    }
}
