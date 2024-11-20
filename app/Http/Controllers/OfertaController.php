<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oferta;
use App\Models\OfertaDetalle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class OfertaController extends Controller
{
    // Obtener todas las ofertas con sus relaciones
    public function index()
    {
        return response()->json(
            Oferta::with([
                'produccion.terreno',
                'produccion.producto',
                'produccion.unidadMedida',
                'detalles.unidadMedida'
            ])->get(),
            200
        );
    }

    // Crear una nueva oferta
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_produccion' => 'required|exists:produccions,id',
                'fecha_creacion' => 'required|date',
                'fecha_expiracion' => 'required|date|after_or_equal:fecha_creacion',
                'estado' => 'sometimes|required|string|in:activo,inactivo',
            ], [
                'id_produccion.required' => 'El campo id_produccion es obligatorio.',
                'id_produccion.exists' => 'La producción especificada no existe.',
                'fecha_creacion.required' => 'El campo fecha de creación es obligatorio.',
                'fecha_expiracion.required' => 'El campo fecha de expiración es obligatorio.',
                'fecha_expiracion.after_or_equal' => 'La fecha de expiración debe ser igual o posterior a la fecha de creación.',
                'estado.in' => 'El campo estado solo puede ser activo e inactivo.'
            ]);

            $oferta = Oferta::create($request->all());
            return response()->json($oferta, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Mostrar detalles de una oferta específica con sus relaciones
    public function show($id)
    {
        try {
            $oferta = Oferta::with([
                'produccion.terreno',
                'produccion.producto',
                'produccion.unidadMedida',
                'detalles.unidadMedida'
            ])->findOrFail($id);
            return response()->json($oferta, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Oferta no encontrada'], 404);
        }
    }

    // Actualizar datos de una oferta
    public function update(Request $request, $id)
    {
        try {
            $oferta = Oferta::findOrFail($id);

            $request->validate([
                'id_produccion' => 'sometimes|required|exists:produccions,id',
                'fecha_creacion' => 'sometimes|required|date',
                'fecha_expiracion' => 'sometimes|required|date|after_or_equal:fecha_creacion',
                'estado' => 'sometimes|required|string|in:activo,inactivo',
            ], [
                'id_produccion.exists' => 'La producción especificada no existe.',
                'fecha_expiracion.after_or_equal' => 'La fecha de expiración debe ser igual o posterior a la fecha de creación.',
                'estado.in' => 'El estado solo puede ser activo e inactivo.'
            ]);

            $oferta->update($request->all());
            return response()->json($oferta, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Oferta no encontrada'], 404);
        }
    }

    // Eliminar una oferta
    public function destroy($id)
    {
        try {
            $oferta = Oferta::findOrFail($id);
            $oferta->delete();
            return response()->json(['message' => 'Oferta eliminada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Oferta no encontrada'], 404);
        }
    }

    // Obtener todos los detalles de una oferta específica
    public function getDetalles($id)
    {
        try {
            $oferta = Oferta::findOrFail($id);
            $detalles = OfertaDetalle::where('id_oferta', $id)->get();

            if ($detalles->isEmpty()) {
                return response()->json(['message' => 'No se encontraron detalles para esta oferta'], 404);
            }

            return response()->json($detalles, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Oferta no encontrada'], 404);
        }
    }

    // Función adicional: Obtener ofertas activas (no expiradas)
    public function getOfertasActivas()
    {
        $ofertas = Oferta::where('fecha_expiracion', '>=', now())->where('estado', 'activo')->get();

        if ($ofertas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron ofertas activas'], 404);
        }

        return response()->json($ofertas, 200);
    }

    // Función adicional: Obtener ofertas por producción
    public function getOfertasByProduccion($produccionId)
    {
        $ofertas = Oferta::where('id_produccion', $produccionId)->get();

        if ($ofertas->isEmpty()) {
            return response()->json(['message' => 'No se encontraron ofertas para esta producción'], 404);
        }

        return response()->json($ofertas, 200);
    }

    // Función adicional: Extender la fecha de expiración de una oferta
    public function extendExpiracion(Request $request, $id)
    {
        try {
            $oferta = Oferta::findOrFail($id);

            $request->validate([
                'nueva_fecha_expiracion' => 'required|date|after:fecha_expiracion'
            ], [
                'nueva_fecha_expiracion.required' => 'La nueva fecha de expiración es obligatoria.',
                'nueva_fecha_expiracion.after' => 'La nueva fecha de expiración debe ser posterior a la fecha actual de expiración.'
            ]);

            $oferta->fecha_expiracion = $request->nueva_fecha_expiracion;
            $oferta->save();

            return response()->json(['message' => 'Fecha de expiración actualizada', 'oferta' => $oferta], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Oferta no encontrada'], 404);
        }
    }
}
