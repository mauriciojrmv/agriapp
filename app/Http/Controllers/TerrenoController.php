<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terreno;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TerrenoController extends Controller
{
    // Obtener todos los terrenos con agricultor y producciones
    public function index()
    {
        return response()->json(Terreno::with(['agricultor', 'producciones'])->get(), 200);
    }

    // Crear un nuevo terreno
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_agricultor' => 'required|exists:agricultors,id',
                'descripcion' => 'required|string|max:255',
                'area' => 'required|numeric|min:0',
                'superficie_total' => 'required|numeric|min:0',
                'ubicacion_latitud' => 'required|numeric',
                'ubicacion_longitud' => 'required|numeric'
            ], [
                'id_agricultor.required' => 'El campo id_agricultor es obligatorio.',
                'id_agricultor.exists' => 'El agricultor especificado no existe.',
                'descripcion.required' => 'El campo descripción es obligatorio.',
                'area.required' => 'El campo área es obligatorio.',
                'area.numeric' => 'El campo área debe ser un número.',
                'superficie_total.required' => 'El campo superficie total es obligatorio.',
                'superficie_total.numeric' => 'El campo superficie total debe ser un número.',
                'ubicacion_latitud.required' => 'La latitud de ubicación es obligatoria.',
                'ubicacion_longitud.required' => 'La longitud de ubicación es obligatoria.'
            ]);

            $terreno = Terreno::create($request->all());
            return response()->json($terreno, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de un terreno específico con agricultor y producciones
    public function show($id)
    {
        try {
            $terreno = Terreno::with(['agricultor', 'producciones'])->findOrFail($id);
            return response()->json($terreno, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Terreno no encontrado'], 404);
        }
    }

    // Actualizar datos de un terreno
    public function update(Request $request, $id)
    {
        try {
            $terreno = Terreno::findOrFail($id);

            $request->validate([
                'id_agricultor' => 'sometimes|required|exists:agricultors,id',
                'descripcion' => 'sometimes|required|string|max:255',
                'area' => 'sometimes|required|numeric|min:0',
                'superficie_total' => 'sometimes|required|numeric|min:0',
                'ubicacion_latitud' => 'sometimes|required|numeric',
                'ubicacion_longitud' => 'sometimes|required|numeric'
            ], [
                'id_agricultor.exists' => 'El agricultor especificado no existe.',
                'descripcion.required' => 'El campo descripción es obligatorio.',
                'area.numeric' => 'El campo área debe ser un número.',
                'superficie_total.numeric' => 'El campo superficie total debe ser un número.',
                'ubicacion_latitud.numeric' => 'La latitud debe ser un número.',
                'ubicacion_longitud.numeric' => 'La longitud debe ser un número.'
            ]);

            $terreno->update($request->all());
            return response()->json($terreno, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Terreno no encontrado'], 404);
        }
    }

    // Eliminar un terreno
    public function destroy($id)
    {
        try {
            $terreno = Terreno::findOrFail($id);
            $terreno->delete();
            return response()->json(['message' => 'Terreno eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Terreno no encontrado'], 404);
        }
    }

    // Obtener todas las producciones relacionadas con un terreno específico
    public function getProducciones($id)
    {
        try {
            $terreno = Terreno::findOrFail($id);
            $producciones = $terreno->producciones;

            if ($producciones->isEmpty()) {
                return response()->json(['message' => 'No se encontraron producciones para este terreno'], 404);
            }

            return response()->json($producciones, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Terreno no encontrado'], 404);
        }
    }
}
