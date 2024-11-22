<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RutaOferta;
use App\Models\RutaCargaOferta;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class RutaOfertaController extends Controller
{
    // Obtener todas las rutas de oferta con sus cargas y detalles relevantes
    public function index()
    {
        return response()->json(
            RutaOferta::with([
                'rutaCargaOferta.cargaOferta.ofertaDetalle.produccion.producto'
            ])->get(),
            200
        );
    }

    // Crear una nueva ruta de oferta
    public function store(Request $request)
    {
        try {
            $request->validate([
                'fecha_recogida' => 'required|date',
                'capacidad_utilizada' => 'required|numeric|min:0',
                'distancia_total' => 'required|numeric|min:0',
                'estado' => 'required|string|max:255'
            ]);

            $rutaOferta = RutaOferta::create($request->all());
            return response()->json($rutaOferta, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una ruta de oferta específica con cargas y detalles relevantes
    public function show($id)
    {
        try {
            $rutaOferta = RutaOferta::with([
                'rutaCargaOferta.cargaOferta.ofertaDetalle.produccion.producto'
            ])->findOrFail($id);

            return response()->json($rutaOferta, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ruta de oferta no encontrada'], 404);
        }
    }

    // Actualizar datos de una ruta de oferta
    public function update(Request $request, $id)
    {
        try {
            $rutaOferta = RutaOferta::findOrFail($id);

            $request->validate([
                'fecha_recogida' => 'sometimes|required|date',
                'capacidad_utilizada' => 'sometimes|required|numeric|min:0',
                'distancia_total' => 'sometimes|required|numeric|min:0',
                'estado' => 'sometimes|required|string|max:255'
            ]);

            $rutaOferta->update($request->all());
            return response()->json($rutaOferta, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ruta de oferta no encontrada'], 404);
        }
    }

    // Eliminar una ruta de oferta
    public function destroy($id)
    {
        try {
            $rutaOferta = RutaOferta::findOrFail($id);

            // Eliminar también las cargas de oferta asociadas a esta ruta
            RutaCargaOferta::where('id_ruta_oferta', $id)->delete();

            $rutaOferta->delete();
            return response()->json(['message' => 'Ruta de oferta eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ruta de oferta no encontrada'], 404);
        }
    }

    // Obtener todas las cargas asociadas con una ruta de oferta específica con detalles relevantes
    public function getCargasOfertas($id)
    {
        try {
            $rutaOferta = RutaOferta::findOrFail($id);
            $cargas = RutaCargaOferta::where('id_ruta_oferta', $id)
                    ->with('cargaOferta.ofertaDetalle.produccion.producto')
                    ->get();

            if ($cargas->isEmpty()) {
                return response()->json(['message' => 'No se encontraron cargas para esta ruta de oferta'], 404);
            }

            return response()->json($cargas, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ruta de oferta no encontrada'], 404);
        }
    }
}
