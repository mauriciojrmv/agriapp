<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class UnidadMedidaController extends Controller
{
    // Obtener todas las unidades de medida
    public function index()
    {
        return response()->json(UnidadMedida::all(), 200);
    }

    // Crear una nueva unidad de medida
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255|unique:unidad_medidas,nombre',
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'nombre.unique' => 'El nombre de la unidad de medida ya existe.'
            ]);

            $unidadMedida = UnidadMedida::create($request->all());
            return response()->json($unidadMedida, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una unidad de medida específica
    public function show($id)
    {
        try {
            $unidadMedida = UnidadMedida::findOrFail($id);
            return response()->json($unidadMedida, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Unidad de medida no encontrada'], 404);
        }
    }

    // Actualizar datos de una unidad de medida
    public function update(Request $request, $id)
    {
        try {
            $unidadMedida = UnidadMedida::findOrFail($id);

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255|unique:unidad_medidas,nombre,' . $id,
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'nombre.unique' => 'El nombre de la unidad de medida ya existe.'
            ]);

            $unidadMedida->update($request->all());
            return response()->json($unidadMedida, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Unidad de medida no encontrada'], 404);
        }
    }

    // Eliminar una unidad de medida
    public function destroy($id)
    {
        try {
            $unidadMedida = UnidadMedida::findOrFail($id);
            $unidadMedida->delete();
            return response()->json(['message' => 'Unidad de medida eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Unidad de medida no encontrada'], 404);
        }
    }
}
