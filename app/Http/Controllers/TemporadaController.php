<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Temporada;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TemporadaController extends Controller
{
    // Obtener todas las temporadas con sus producciones
    public function index()
    {
        return response()->json(Temporada::with('producciones')->get(), 200);
    }

    // Crear una nueva temporada
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'descripcion' => 'nullable|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'fecha_inicio.required' => 'El campo fecha de inicio es obligatorio.',
                'fecha_fin.required' => 'El campo fecha de fin es obligatorio.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.'
            ]);

            $temporada = Temporada::create($request->all());
            return response()->json($temporada, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una temporada específica con sus producciones
    public function show($id)
    {
        try {
            $temporada = Temporada::with('producciones')->findOrFail($id);
            return response()->json($temporada, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Temporada no encontrada'], 404);
        }
    }

    // Actualizar datos de una temporada
    public function update(Request $request, $id)
    {
        try {
            $temporada = Temporada::findOrFail($id);

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
                'descripcion' => 'nullable|string'
            ], [
                'nombre.required' => 'El campo nombre es obligatorio.',
                'fecha_inicio.required' => 'El campo fecha de inicio es obligatorio.',
                'fecha_fin.required' => 'El campo fecha de fin es obligatorio.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.'
            ]);

            $temporada->update($request->all());
            return response()->json($temporada, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Temporada no encontrada'], 404);
        }
    }

    // Eliminar una temporada
    public function destroy($id)
    {
        try {
            $temporada = Temporada::findOrFail($id);
            $temporada->delete();
            return response()->json(['message' => 'Temporada eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Temporada no encontrada'], 404);
        }
    }

    // Obtener todas las producciones de una temporada específica
    public function getProducciones($id)
    {
        try {
            $temporada = Temporada::findOrFail($id);
            $producciones = $temporada->producciones;

            if ($producciones->isEmpty()) {
                return response()->json(['message' => 'No se encontraron producciones para esta temporada'], 404);
            }

            return response()->json($producciones, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Temporada no encontrada'], 404);
        }
    }
}
